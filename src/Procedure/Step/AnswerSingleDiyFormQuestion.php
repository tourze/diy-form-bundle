<?php

namespace DiyFormBundle\Procedure\Step;

use DiyFormBundle\Entity\Data;
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

#[MethodDoc('提交单条答题信息')]
#[MethodTag('动态表单')]
#[MethodExpose('AnswerSingleDiyFormQuestion')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Log]
class AnswerSingleDiyFormQuestion extends LockableProcedure
{
    #[MethodParam('表单ID')]
    public string $formId = '2';

    #[MethodParam('记录ID')]
    public int $recordId;

    #[MethodParam('题目/字段ID，如果是希望拿第一题，那这里可以不传入')]
    public int $fieldId;

    #[MethodParam('输入/选择值，如果是希望拿第一题，那这里可以不传入')]
    public string|array|int $input = '';

    #[MethodParam('是否跳过这个题目，跳过的话input可以不传入')]
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
        $form = $this->formRepository->findOneBy([
            'id' => $this->formId,
            'valid' => true,
        ]);
        if (null === $form) {
            throw new ApiException('找不到表单');
        }

        $record = $this->recordRepository->findOneBy([
            'id' => $this->recordId,
            'form' => $form,
            'user' => $this->security->getUser(),
        ]);
        if (null === $record) {
            throw new ApiException('找不到答题记录');
        }

        $inputField = $this->fieldRepository->findOneBy([
            'id' => $this->fieldId,
            'form' => $form,
        ]);
        if (null === $inputField) {
            throw new ApiException('找不到指定题目/字段');
        }

        $event = new BeforeAnswerSingleDiyFormEvent();
        $event->setField($inputField);
        $event->setUser($this->security->getUser());
        $event->setInput(is_array($this->input) ? Json::encode($this->input) : (string) $this->input);
        $this->eventDispatcher->dispatch($event);

        $this->entityManager->wrapInTransaction(function () use ($record, $inputField, $form) {
            // 如果已经答过，就当做更新
            $data = $this->dataRepository->findOneBy([
                'record' => $record,
                'field' => $inputField,
            ]);
            if (null === $data) {
                $data = new Data();
                $data->setRecord($record);
                $data->setField($inputField);
            }

            // 可能会有返回逻辑，例如已经回答了 1,2,3,4 前端此时可能会返回到第 1 题，重新作答，此时我们需要将后面的提交数据全部删除
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
                    ->execute();
            }

            $answerTags = $this->tagCalculator->findByRecord($record);
            $data->setAnswerTags($answerTags);

            $data->setInput(is_array($this->input) ? Json::encode($this->input) : strval($this->input));
            $data->setSkip($this->skip);
            $this->entityManager->persist($data);
            $this->entityManager->flush();
        });

        return [
            '__message' => '答题成功',
        ];
    }

    public static function getMockResult(): ?array
    {
        return [
            '__message' => '答题成功',
        ];
    }
}
