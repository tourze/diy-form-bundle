<?php

namespace DiyFormBundle\Procedure\Record;

use DiyFormBundle\Event\GetRecordDetailEvent;
use DiyFormBundle\Event\RecordAfterAnalyseEvent;
use DiyFormBundle\Event\RecordAnalyseTriggerEvent;
use DiyFormBundle\Event\RecordBeforeAnalyseEvent;
use DiyFormBundle\Event\RecordFormatEvent;
use DiyFormBundle\Repository\RecordRepository;
use DiyFormBundle\Service\ExpressionService;
use DiyFormBundle\Service\TagCalculator;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[MethodDoc(summary: '获取用户的表单提交记录')]
#[MethodTag(name: '动态表单')]
#[MethodExpose(method: 'GetDiyFormRecordDetail')]
#[WithMonologChannel(channel: 'procedure')]
class GetDiyFormRecordDetail extends BaseProcedure
{
    #[MethodParam(description: '记录ID')]
    public string $recordId;

    public function __construct(
        private readonly RecordRepository $recordRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly ExpressionService $expressionService,
        private readonly TagCalculator $tagCalculator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(): array
    {
        $expressionLanguage = new ExpressionLanguage();

        $record = $this->recordRepository->findOneBy([
            'id' => $this->recordId,
            // TODO 因为去除了用户条件判断，所以这里有平行绕过漏洞
        ]);
        if (null === $record) {
            throw new ApiException('查找不到提交记录');
        }

        $event = new RecordFormatEvent();
        $event->setRecord($record);
        $event->setResult($this->normalizer->normalize($record, 'array', ['groups' => 'restful_read']));
        $this->eventDispatcher->dispatch($event);
        $result = $event->getResult();

        $result['analyses'] = null;

        $event = new RecordBeforeAnalyseEvent();
        $event->setRecord($record);
        $event->setResult($result);
        $this->eventDispatcher->dispatch($event);
        $result = $event->getResult();

        if (null === $result['analyses']) {
            $result['analyses'] = [];
            $answerTags = $this->tagCalculator->findByRecord($record);
            $this->expressionService->bindRecordFunction($expressionLanguage, $record, $answerTags);

            // 这里补充上报告部分
            $values = [
                'form' => $record->getForm(),
                'record' => $record,
            ];

            foreach ($record->getForm()->getSortedAnalyses() as $analysis) {
                // 如果满足条件，就返回给前端
                $expressionStatement = trim($analysis->getRule());
                if (empty($expressionStatement)) {
                    $res = true;
                } else {
                    $res = $expressionLanguage->evaluate($expressionStatement, $values);
                    $this->logger->debug("运算表达式计算：{$expressionStatement}", [
                        'result' => $res,
                        'values' => $values,
                    ]);
                }

                if (true === $res) {
                    if (!isset($result['analyses'][$analysis->getCategory()])) {
                        $result['analyses'][$analysis->getCategory()] = [];
                    }

                    $tmp = $this->normalizer->normalize($analysis, 'array', ['groups' => 'restful_read']);
                    $event = new RecordAnalyseTriggerEvent();
                    $event->setAnalyse($analysis);
                    $event->setRecord($record);
                    $event->setResult($tmp);
                    $this->eventDispatcher->dispatch($event);

                    // 这里特意使用标题做一层去重
                    $result['analyses'][$analysis->getCategory()][$analysis->getTitle()] = $event->getResult();
                }
            }
        }

        $event = new RecordAfterAnalyseEvent();
        $event->setRecord($record);
        $event->setResult($result);
        $this->eventDispatcher->dispatch($event);
        $result = $event->getResult();

        $event = new GetRecordDetailEvent();
        $event->setRecord($record);
        $event->setResult($result);
        $this->eventDispatcher->dispatch($event);

        return $event->getResult();
    }
}
