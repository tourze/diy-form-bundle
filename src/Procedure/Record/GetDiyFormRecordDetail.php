<?php

declare(strict_types=1);

namespace DiyFormBundle\Procedure\Record;

use DiyFormBundle\Entity\Analyse;
use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Event\GetRecordDetailEvent;
use DiyFormBundle\Event\RecordAfterAnalyseEvent;
use DiyFormBundle\Event\RecordAnalyseTriggerEvent;
use DiyFormBundle\Event\RecordBeforeAnalyseEvent;
use DiyFormBundle\Event\RecordFormatEvent;
use DiyFormBundle\Param\Record\GetDiyFormRecordDetailParam;
use DiyFormBundle\Repository\RecordRepository;
use DiyFormBundle\Service\ExpressionEngineService;
use DiyFormBundle\Service\TagCalculator;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPC\Core\Result\ArrayResult;

#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[MethodDoc(summary: '获取用户的表单提交记录')]
#[MethodTag(name: '动态表单')]
#[MethodExpose(method: 'GetDiyFormRecordDetail')]
#[WithMonologChannel(channel: 'procedure')]
class GetDiyFormRecordDetail extends BaseProcedure
{
    public function __construct(
        private readonly RecordRepository $recordRepository,
        private readonly ExpressionEngineService $expressionService,
        private readonly TagCalculator $tagCalculator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @phpstan-param GetDiyFormRecordDetailParam $param
     */
    public function execute(GetDiyFormRecordDetailParam|RpcParamInterface $param): ArrayResult
    {
        $record = $this->findRecord($param);
        $result = $this->formatRecord($record);
        $result = $this->processAnalyses($record, $result);

        return $this->finalizeResult($record, $result);
    }

    private function findRecord(GetDiyFormRecordDetailParam $param): Record
    {
        $record = $this->recordRepository->findOneBy([
            'id' => $param->recordId,
            // TODO 因为去除了用户条件判断，所以这里有平行绕过漏洞
        ]);

        if (null === $record) {
            throw new ApiException('查找不到提交记录');
        }

        return new ArrayResult($record);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatRecord(Record $record): array
    {
        $event = new RecordFormatEvent();
        $event->setRecord($record);

        $result = [
            'finished' => $record->isFinished(),
            'startTime' => $record->getStartTime()?->format('c'),
            'finishTime' => $record->getFinishTime()?->format('c'),
            'dataList' => $this->formatDataList($record->getDataList()),
        ];

        $event->setResult($result);
        $this->eventDispatcher->dispatch($event);

        return $event->getResult();
    }

    /**
     * @param array<string, Data> $dataList
     * @return array<string, array<string, mixed>>
     */
    private function formatDataList(array $dataList): array
    {
        $result = [];
        foreach ($dataList as $sn => $data) {
            $result[$sn] = [
                'field' => $data->getField()?->retrieveApiArray(),
                'input' => $data->getInput(),
                'inputArray' => $data->getInputArray(),
                'skip' => $data->isSkip(),
            ];
        }

        return new ArrayResult($result);
    }

    /**
     * @param array<string, mixed> $result
     * @return array<string, mixed>
     */
    private function processAnalyses(Record $record, array $result): array
    {
        $result['analyses'] = null;

        $event = new RecordBeforeAnalyseEvent();
        $event->setRecord($record);
        $event->setResult($result);
        $this->eventDispatcher->dispatch($event);
        $result = $event->getResult();

        if (null === $result['analyses']) {
            $result['analyses'] = [];
            $result = $this->processRecordAnalyses($record, $result);
        }

        return new ArrayResult($result);
    }

    /**
     * @param array<string, mixed> $result
     * @return array<string, mixed>
     */
    private function processRecordAnalyses(Record $record, array $result): array
    {
        $answerTags = $this->tagCalculator->findByRecord($record);

        $form = $record->getForm();
        if (null === $form) {
            return new ArrayResult($result);
        }

        foreach ($form->getSortedAnalyses() as $analysis) {
            $result = $this->processAnalysis($analysis, $record, $answerTags, $result);
        }

        return new ArrayResult($result);
    }

    /**
     * @param array<string> $answerTags
     * @param array<string, mixed> $result
     * @return array<string, mixed>
     */
    private function processAnalysis(Analyse $analysis, Record $record, array $answerTags, array $result): array
    {
        $rule = $analysis->getRule();
        $expressionStatement = trim($rule);
        // 将 array<string> 转换为 array<string, mixed>
        $answerTagsMap = array_fill_keys($answerTags, true);
        $res = ('' === $expressionStatement)
            ? true
            : $this->expressionService->evaluateWithRecord($expressionStatement, $record, $answerTagsMap);

        if ('' !== $expressionStatement) {
            $this->logger->debug("运算表达式计算：{$expressionStatement}", [
                'result' => $res,
                'record' => $record,
                'answerTags' => $answerTags,
            ]);
        }

        if (true === $res) {
            $category = $analysis->getCategory();
            // 确保 $result['analyses'] 是数组
            if (!isset($result['analyses']) || !is_array($result['analyses'])) {
                $result['analyses'] = [];
            }
            if (null === $category) {
                $category = 'default';
            }
            if (!isset($result['analyses'][$category])) {
                $result['analyses'][$category] = [];
            }

            $tmp = $analysis->retrieveApiArray();

            $event = new RecordAnalyseTriggerEvent();
            $event->setAnalyse($analysis);
            $event->setRecord($record);
            $event->setResult($tmp);
            $this->eventDispatcher->dispatch($event);

            $title = $analysis->getTitle();
            // 获取类型安全的 analyses 数组
            /** @var array<string, array<string, mixed>> $analyses */
            $analyses = $result['analyses'];
            $analyses[$category][$title] = $event->getResult();
            $result['analyses'] = $analyses;
        }

        return new ArrayResult($result);
    }

    /**
     * @param array<string, mixed> $result
     * @return array<string, mixed>
     */
    private function finalizeResult(Record $record, array $result): array
    {
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
