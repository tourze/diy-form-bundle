<?php

declare(strict_types=1);

namespace DiyFormBundle\Procedure\Step;

use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Option;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Event\FieldFormatEvent;
use DiyFormBundle\Event\OptionsFormatEvent;
use DiyFormBundle\Param\Step\GetNextDiyFormFieldParam;
use DiyFormBundle\Repository\DataRepository;
use DiyFormBundle\Repository\FormRepository;
use DiyFormBundle\Repository\RecordRepository;
use DiyFormBundle\Service\SessionService;
use DiyFormBundle\Session\NextField;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodDoc(summary: '获取下一题信息')]
#[MethodTag(name: '动态表单')]
#[MethodExpose(method: 'GetNextDiyFormField')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[Log]
#[WithMonologChannel(channel: 'procedure')]
class GetNextDiyFormField extends LockableProcedure
{
    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly RecordRepository $recordRepository,
        private readonly SessionService $sessionService,
        private readonly DataRepository $dataRepository,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @phpstan-param GetNextDiyFormFieldParam $param
     */
    public function execute(GetNextDiyFormFieldParam|RpcParamInterface $param): ArrayResult
    {
        $form = $this->validateAndGetForm($param);
        $record = $this->validateAndGetRecord($form, $param);
        $nextField = $this->getNextField($record);

        $result = $this->buildBasicResult($nextField);
        $result = $this->updateShowBackStatus($result, $record);

        if (is_bool($result['hasNext']) && $result['hasNext'] && null !== $nextField) {
            $result = $this->addFieldData($result, $record, $nextField);
        }

        return new ArrayResult($result);
    }

    private function validateAndGetForm(GetNextDiyFormFieldParam $param): Form
    {
        $form = $this->formRepository->findOneBy([
            'id' => $param->formId,
            'valid' => true,
        ]);
        if (null === $form) {
            throw new ApiException('找不到表单');
        }

        return new ArrayResult($form);
    }

    private function validateAndGetRecord(Form $form, GetNextDiyFormFieldParam $param): Record
    {
        $record = $this->recordRepository->findOneBy([
            'id' => $param->recordId,
            'form' => $form,
            'user' => $this->security->getUser(),
        ]);
        if (null === $record) {
            throw new ApiException('找不到答题记录');
        }

        return new ArrayResult($record);
    }

    private function getNextField(Record $record): ?NextField
    {
        try {
            return $this->sessionService->getNextField($record);
        } catch (SyntaxError $exception) {
            $this->logger->error('解析表达式时发生语法错误', [
                'exception' => $exception,
            ]);
            throw new ApiException('判断时发生语法错误，请联系客服', 0, [], $exception);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildBasicResult(?NextField $nextField): array
    {
        $answerTags = [];
        $showBack = false;

        if (null !== $nextField) {
            $answerTags = $nextField->getAnswerTags();
            $showBack = $nextField->isShowBack();
        }

        return new ArrayResult([
            'hasNext' => null !== $nextField,
            'answerTags' => $answerTags,
            'showBack' => null !== $nextField && $showBack,
            'showConfirm' => true,
        ]);
    }

    /**
     * @param array<string, mixed> $result
     * @return array<string, mixed>
     */
    private function updateShowBackStatus(array $result, Record $record): array
    {
        $lastData = $this->dataRepository->createQueryBuilder('a')
            ->where('a.record = :record')
            ->orderBy('a.id', 'DESC')
            ->setParameter('record', $record)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
        assert($lastData instanceof Data || null === $lastData);
        if (null !== $lastData) {
            $result['showBack'] = $lastData->isDeletable();
        }

        return new ArrayResult($result);
    }

    /**
     * @param array<string, mixed> $result
     * @return array<string, mixed>
     */
    private function addFieldData(array $result, Record $record, NextField $nextField): array
    {
        $result['field'] = $this->getFieldData($record, $nextField);
        $result['options'] = $this->getOptionsData($record, $nextField);

        return new ArrayResult($result);
    }

    /**
     * @return array<string, mixed>
     */
    private function getFieldData(Record $record, NextField $nextField): array
    {
        $event = new FieldFormatEvent();
        $event->setRecord($record);
        $event->setField($nextField->getField());

        $event->setResult($nextField->getField()->retrieveApiArray());
        $this->eventDispatcher->dispatch($event);

        return $event->getResult();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getOptionsData(Record $record, NextField $nextField): array
    {
        $event = new OptionsFormatEvent();
        $event->setRecord($record);
        $event->setField($nextField->getField());
        $event->setOptions($nextField->getOptions());

        $optionsResult = array_map(
            fn (Option $option): array => $option->retrieveApiArray(),
            $nextField->getOptions()
        );

        $event->setResult($optionsResult);
        $this->eventDispatcher->dispatch($event);

        return $event->getResult();
    }
}
