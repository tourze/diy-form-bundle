<?php

namespace DiyFormBundle\Procedure\Step;

use DiyFormBundle\Entity\Data;
use DiyFormBundle\Event\FieldFormatEvent;
use DiyFormBundle\Event\OptionsFormatEvent;
use DiyFormBundle\Repository\DataRepository;
use DiyFormBundle\Repository\FormRepository;
use DiyFormBundle\Repository\RecordRepository;
use DiyFormBundle\Service\SessionService;
use Doctrine\Common\Collections\Criteria;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodDoc('获取下一题信息')]
#[MethodTag('动态表单')]
#[MethodExpose('GetNextDiyFormField')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Log]
class GetNextDiyFormField extends LockableProcedure
{
    #[MethodParam('表单ID')]
    public int $formId = 2;

    #[MethodParam('记录ID')]
    public int $recordId;

    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly RecordRepository $recordRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly SessionService $sessionService,
        private readonly DataRepository $dataRepository,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $procedureLogger,
    ) {
    }

    public function execute(): array
    {
        $form = $this->formRepository->findOneBy([
            'id' => $this->formId,
            'valid' => true,
        ]);
        if (!$form) {
            throw new ApiException('找不到表单');
        }

        $record = $this->recordRepository->findOneBy([
            'id' => $this->recordId,
            'form' => $form,
            'user' => $this->security->getUser(),
        ]);
        if (!$record) {
            throw new ApiException('找不到答题记录');
        }

        try {
            $nextField = $this->sessionService->getNextField($record);
        } catch (SyntaxError $exception) {
            $this->procedureLogger->error('解析表达式时发生语法错误', [
                'exception' => $exception,
            ]);
            throw new ApiException('判断时发生语法错误，请联系客服', 0, [], $exception);
        }

        $result = [
            'hasNext' => null !== $nextField,
            'answerTags' => $nextField ? $nextField->getAnswerTags() : [],
            'showBack' => $nextField && $nextField->isShowBack(),
            'showConfirm' => true, // 基本都有的
        ];

        // 如果这个人上一次回答的题目，是一个不可删除的题目，我们就不要让他返回上一题了
        /** @var Data|null $lastData */
        $lastData = $this->dataRepository->createQueryBuilder('a')
            ->where('a.record = :record')
            ->orderBy('a.id', Criteria::DESC)
            ->setParameter('record', $record)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if ($lastData) {
            $result['showBack'] = $lastData->isDeletable();
        }

        if ($result['hasNext']) {
            $event = new FieldFormatEvent();
            $event->setRecord($record);
            $event->setField($nextField->getField());
            $event->setResult($this->normalizer->normalize($nextField->getField(), 'array', ['groups' => 'restful_read']));
            $this->eventDispatcher->dispatch($event);
            $result['field'] = $event->getResult();

            $event = new OptionsFormatEvent();
            $event->setRecord($record);
            $event->setField($nextField->getField());
            $event->setOptions($nextField->getOptions());
            $event->setResult($this->normalizer->normalize($nextField->getOptions(), 'array', ['groups' => 'restful_read']));
            $this->eventDispatcher->dispatch($event);
            $result['options'] = $event->getResult();
        }

        return $result;
    }
}
