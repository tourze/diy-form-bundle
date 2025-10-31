<?php

declare(strict_types=1);

namespace DiyFormBundle\Procedure\Step;

use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Event\BeforeAnswerSingleDiyFormEvent;
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
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
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
    #[MethodParam(description: '表单ID')]
    public string $formId = '2';

    #[MethodParam(description: '记录ID')]
    public int $recordId;

    #[MethodParam(description: '题目/字段ID，如果是希望拿第一题，那这里可以不传入')]
    public int $fieldId;

    /** @var string|array<int, mixed>|int */
    #[MethodParam(description: '输入/选择值，如果是希望拿第一题，那这里可以不传入')]
    public $input = '';

    #[MethodParam(description: '是否跳过这个题目，跳过的话input可以不传入')]
    public bool $skip = false;

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

    public function execute(): array
    {
        $form = $this->validateAndGetForm();
        $record = $this->validateAndGetRecord($form);
        $inputField = $this->validateAndGetField($form);

        $this->dispatchBeforeAnswerEvent($inputField);
        $this->processAnswer($record, $inputField, $form);

        return [
            '__message' => '答题成功',
        ];
    }

    private function validateAndGetForm(): Form
    {
        $form = $this->formRepository->findOneBy([
            'id' => $this->formId,
            'valid' => true,
        ]);

        if (null === $form) {
            throw new ApiException('找不到表单');
        }

        return $form;
    }

    private function validateAndGetRecord(Form $form): Record
    {
        $record = $this->recordRepository->findOneBy([
            'id' => $this->recordId,
            'form' => $form,
            'user' => $this->security->getUser(),
        ]);

        if (null === $record) {
            throw new ApiException('找不到答题记录');
        }

        return $record;
    }

    private function validateAndGetField(Form $form): Field
    {
        $inputField = $this->fieldRepository->findOneBy([
            'id' => $this->fieldId,
            'form' => $form,
        ]);

        if (null === $inputField) {
            throw new ApiException('找不到指定题目/字段');
        }

        return $inputField;
    }

    private function dispatchBeforeAnswerEvent(Field $inputField): void
    {
        $event = new BeforeAnswerSingleDiyFormEvent();
        $event->setField($inputField);
        $user = $this->security->getUser();
        if (null !== $user) {
            $event->setUser($user);
        }
        $event->setInput(is_array($this->input) ? Json::encode($this->input) : (string) $this->input);
        $this->eventDispatcher->dispatch($event);
    }

    private function processAnswer(Record $record, Field $inputField, Form $form): void
    {
        $this->entityManager->wrapInTransaction(function () use ($record, $inputField, $form): void {
            $data = $this->getOrCreateDataEntry($record, $inputField);
            $this->cleanupLaterFields($record, $inputField, $form);
            $this->updateDataEntry($data, $record);
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

        return $data;
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

    private function updateDataEntry(Data $data, Record $record): void
    {
        $answerTags = $this->tagCalculator->findByRecord($record);
        // Convert array<string> to list<string> for setAnswerTags
        $data->setAnswerTags(array_values($answerTags));
        $data->setInput(is_array($this->input) ? Json::encode($this->input) : strval($this->input));
        $data->setSkip($this->skip);
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public static function getMockResult(): ?array
    {
        return [
            '__message' => '答题成功',
        ];
    }
}
