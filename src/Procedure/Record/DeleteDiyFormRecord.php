<?php

namespace DiyFormBundle\Procedure\Record;

use DiyFormBundle\Repository\RecordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[MethodDoc('删除用户的表单记录')]
#[MethodTag('动态表单')]
#[MethodExpose('DeleteDiyFormRecord')]
#[Log]
class DeleteDiyFormRecord extends LockableProcedure
{
    #[MethodParam('记录ID')]
    public string $recordId;

    public function __construct(
        private readonly RecordRepository $recordRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $record = $this->recordRepository->findOneBy([
            'id' => $this->recordId,
            'user' => $this->security->getUser(),
        ]);
        if (!$record) {
            throw new ApiException('查找不到提交记录');
        }

        $this->entityManager->remove($record);
        $this->entityManager->flush();

        return [
            '__message' => '删除成功',
        ];
    }
}
