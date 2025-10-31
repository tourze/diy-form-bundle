<?php

declare(strict_types=1);

namespace DiyFormBundle\Procedure\Step;

use Carbon\CarbonImmutable;
use DiyFormBundle\Event\SubmitRecordEvent;
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

#[MethodDoc(summary: '保存表单数据', description: '当GetNextDiyFormField返回hasNext=false时，我们就可以调用这个接口来提交数据')]
#[MethodTag(name: '动态表单')]
#[MethodExpose(method: 'SubmitDiyFormRecord')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[Log]
class SubmitDiyFormRecord extends LockableProcedure
{
    #[MethodParam(description: '表单ID')]
    public int $formId = 2;

    #[MethodParam(description: '记录ID，通过CreateDiyFormRecord接口获得')]
    public int $recordId;

    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly RecordRepository $recordRepository,
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
            'user' => $this->security->getUser(),
        ]);
        if (null === $record) {
            throw new ApiException('找不到答题数据');
        }

        $record->setFinished(true);
        $record->setFinishTime(CarbonImmutable::now());
        // 将 array<string> 转换为 array<string, mixed>
        $answerTags = $this->tagCalculator->findByRecord($record);
        $record->setAnswerTags(array_fill_keys($answerTags, true));
        $this->entityManager->persist($record);
        $this->entityManager->flush();

        $event = new SubmitRecordEvent();
        $user = $this->security->getUser();
        if (null !== $user) {
            $event->setUser($user);
        }
        $event->setRecord($record);
        $this->eventDispatcher->dispatch($event);

        return [
            '__message' => '提交成功',
            'recordId' => $record->getId(),
        ];
    }
}
