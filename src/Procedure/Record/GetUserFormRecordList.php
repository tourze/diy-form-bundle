<?php

declare(strict_types=1);

namespace DiyFormBundle\Procedure\Record;

use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Event\RecordFormatEvent;
use DiyFormBundle\Param\Record\GetUserFormRecordListParam;
use DiyFormBundle\Repository\FormRepository;
use DiyFormBundle\Repository\RecordRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;

#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[MethodDoc(summary: '获取用户的表单提交记录')]
#[MethodTag(name: '动态表单')]
#[MethodExpose(method: 'GetUserFormRecordList')]
class GetUserFormRecordList extends BaseProcedure
{
    use PaginatorTrait;

    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly RecordRepository $recordRepository,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @phpstan-param GetUserFormRecordListParam $param
     */
    public function execute(GetUserFormRecordListParam|RpcParamInterface $param): ArrayResult
    {
        $qb = $this->recordRepository->createQueryBuilder('a');
        $qb->where('a.user = :user AND a.finished = true'); // 只看已完成的
        $qb->setParameter('user', $this->security->getUser());

        // 过滤
        if (null !== $param->formId) {
            $form = $this->formRepository->findOneBy([
                'id' => $param->formId,
                'valid' => true,
            ]);
            if (null === $form) {
                throw new ApiException('找不到指定表单');
            }
            $qb->andWhere('a.form = :form');
            $qb->setParameter('form', $form);
        }

        $qb->addOrderBy('a.id', 'DESC');

        return new ArrayResult($this->fetchList($qb, $this->formatItem(...), null, $param));
    }

    /**
     * @return array<string, mixed>
     */
    private function formatItem(Record $item): array
    {
        $result = [
            'finished' => $item->isFinished(),
            'startTime' => $item->getStartTime()?->format('c'),
            'finishTime' => $item->getFinishTime()?->format('c'),
            'dataList' => $this->formatDataList($item->getDataList()),
        ];

        $event = new RecordFormatEvent();
        $event->setRecord($item);
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
}
