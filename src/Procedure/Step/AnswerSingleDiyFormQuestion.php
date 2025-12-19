<?php

declare(strict_types=1);

namespace DiyFormBundle\Procedure\Step;

use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Event\BeforeAnswerSingleDiyFormEvent;
use DiyFormBundle\Param\Step\AnswerSingleDiyFormQuestionParam;
use DiyFormBundle\Repository\DataRepository;
use DiyFormBundle\Repository\FieldRepository;
use DiyFormBundle\Repository\FormRepository;
use DiyFormBundle\Repository\RecordRepository;
use DiyFormBundle\Service\TagCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
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
use Yiisoft\Json\Json;

#[MethodDoc(summary: '提交单条答题信息')]
#[MethodTag(name: '动态表单')]
#[MethodExpose(method: 'AnswerSingleDiyFormQuestion')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[Log]
class AnswerSingleDiyFormQuestion extends LockableProcedure
{
    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly FieldRepository $fieldRepository,
        private readonly RecordRepository $recordRepository,
        private readonly DataRepository $dataRepository,
        private readonly TagCalculator $tagCalculator,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @phpstan-param AnswerSingleDiyFormQuestionParam $param
     */
    public function execute(AnswerSingleDiyFormQuestionParam|RpcParamInterface $param): ArrayResult
    {
        $form = $this->validateAndGetForm($param);
        $record = $this->validateAndGetRecord($form, $param);
        $inputField = $this->validateAndGetField($form, $param);

        $this->dispatchBeforeAnswerEvent($inputField, $param);
        $this->processAnswer($record, $inputField, $form, $param);

        return new ArrayResult([
            '__message' => '答题成功',
        ]);
    }

    private function validateAndGetForm(AnswerSingleDiyFormQuestionParam $param): Form
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

    private function validateAndGetRecord(Form $form, AnswerSingleDiyFormQuestionParam $param): Record
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

    private function validateAndGetField(Form $form, AnswerSingleDiyFormQuestionParam $param): Field
    {
        $inputField = $this->fieldRepository->findOneBy([
            'id' => $param->fieldId,
            'form' => $form,
        ]);

        if (null === $inputField) {
            throw new ApiException('找不到指定题目/字段');
        }

        return new ArrayResult($inputField);
    }

    private function dispatchBeforeAnswerEvent(Field $inputField, AnswerSingleDiyFormQuestionParam $param): void
    {
        $event = new BeforeAnswerSingleDiyFormEvent();
        $event->setField($inputField);
        $user = $this->security->getUser();
        if (null !== $user) {
            $event->setUser($user);
        }
        $event->setInput(is_array($param->input) ? Json::encode($param->input) : (string) $param->input);
        $this->eventDispatcher->dispatch($event);
    }

    private function processAnswer(Record $record, Field $inputField, Form $form, AnswerSingleDiyFormQuestionParam $param): void
    {
        $this->entityManager->wrapInTransaction(function () use ($record, $inputField, $form, $param): void {
            $data = $this->getOrCreateDataEntry($record, $inputField);
            $this->cleanupLaterFields($record, $inputField, $form);
            $this->updateDataEntry($data, $record, $param);
        });
    }

    private function getOrCreateDataEntry(Record $record, Field $inputField): Data
    {
        $data = $this->dataRepository->findOneBy([
            'record' => $record,
            'field' => $inputField,
        ]);

        if (null === $data) {
            $data = new Data();
            $data->setRecord($record);
            $data->setField($inputField);
        }

        return new ArrayResult($data);
    }

    private function cleanupLaterFields(Record $record, Field $inputField, Form $form): void
    {
        $sortedFields = $form->getSortedFields();

        foreach ($sortedFields as $k => $sortedField) {
            unset($sortedFields[$k]);
            if ($sortedField->getId() === $inputField->getId()) {
                break;
            }
        }

        foreach ($sortedFields as $sortedField) {
            $this->dataRepository->createQueryBuilder('a')
                ->delete()
                ->where('a.field = :field AND a.record = :record AND a.deletable = true')
                ->setParameter('field', $sortedField)
                ->setParameter('record', $record)
                ->getQuery()
                ->execute()
            ;
        }
    }

    private function updateDataEntry(Data $data, Record $record, AnswerSingleDiyFormQuestionParam $param): void
    {
        $answerTags = $this->tagCalculator->findByRecord($record);
        // Convert array<string> to list<string> for setAnswerTags
        $data->setAnswerTags(array_values($answerTags));
        $data->setInput(is_array($param->input) ? Json::encode($param->input) : strval($param->input));
        $data->setSkip($param->skip);
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
