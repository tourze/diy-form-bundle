<?php

declare(strict_types=1);

namespace DiyFormBundle\Procedure\Step;

use Carbon\CarbonImmutable;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Param\Step\CreateDiyFormRecordParam;
use DiyFormBundle\Repository\FormRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodDoc(summary: '消费者开始答题前，需要先创一个答题记录')]
#[MethodTag(name: '动态表单')]
#[MethodExpose(method: 'CreateDiyFormRecord')]
#[Log]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class CreateDiyFormRecord extends LockableProcedure
{
    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @phpstan-param CreateDiyFormRecordParam $param
     */
    public function execute(CreateDiyFormRecordParam|RpcParamInterface $param): ArrayResult
    {
        $form = $this->formRepository->findOneBy([
            'id' => $param->formId,
            'valid' => true,
        ]);
        if (null === $form) {
            throw new ApiException('找不到表单');
        }

        $record = new Record();
        $record->setForm($form);
        $user = $this->security->getUser();
        if (null !== $user) {
            $record->setUser($user);
        }
        $record->setFinished(false);
        $record->setStartTime(CarbonImmutable::now());
        $this->entityManager->persist($record);
        $this->entityManager->flush();

        return new ArrayResult([
            'id' => $record->getId(),
            'finished' => $record->isFinished(),
            'startTime' => $record->getStartTime()?->format('c'),
            'finishTime' => $record->getFinishTime()?->format('c'),
        ]);
    }
}
