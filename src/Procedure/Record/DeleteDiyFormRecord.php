<?php

declare(strict_types=1);

namespace DiyFormBundle\Procedure\Record;

use DiyFormBundle\Param\Record\DeleteDiyFormRecordParam;
use DiyFormBundle\Repository\RecordRepository;
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

#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[MethodDoc(summary: '删除用户的表单记录')]
#[MethodTag(name: '动态表单')]
#[MethodExpose(method: 'DeleteDiyFormRecord')]
#[Log]
class DeleteDiyFormRecord extends LockableProcedure
{
    public function __construct(
        private readonly RecordRepository $recordRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    ) {
    }

    /**
     * @phpstan-param DeleteDiyFormRecordParam $param
     */
    public function execute(DeleteDiyFormRecordParam|RpcParamInterface $param): ArrayResult
    {
        $record = $this->recordRepository->findOneBy([
            'id' => $param->recordId,
            'user' => $this->security->getUser(),
        ]);
        if (null === $record) {
            throw new ApiException('查找不到提交记录');
        }

        $this->entityManager->remove($record);
        $this->entityManager->flush();

        return new ArrayResult([
            '__message' => '删除成功',
        ]);
    }
}
