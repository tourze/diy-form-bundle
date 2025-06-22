<?php

namespace DiyFormBundle\Procedure\Record;

use DiyFormBundle\Entity\Record;
use DiyFormBundle\Event\RecordFormatEvent;
use DiyFormBundle\Repository\FormRepository;
use DiyFormBundle\Repository\RecordRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[MethodDoc('获取用户的表单提交记录')]
#[MethodTag('动态表单')]
#[MethodExpose('GetUserFormRecordList')]
class GetUserFormRecordList extends BaseProcedure
{
    use PaginatorTrait;

    #[MethodParam('表单ID')]
    public ?string $formId = null;

    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly RecordRepository $recordRepository,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function execute(): array
    {
        $qb = $this->recordRepository->createQueryBuilder('a');
        $qb->where('a.user = :user AND a.finished = true'); // 只看已完成的
        $qb->setParameter('user', $this->security->getUser());

        // 过滤
        if (null !== $this->formId) {
            $form = $this->formRepository->findOneBy([
                'id' => $this->formId,
                'valid' => true,
            ]);
            if (null === $form) {
                throw new ApiException('找不到指定表单');
            }
            $qb->andWhere('a.form = :form');
            $qb->setParameter('form', $form);
        }

        $qb->addOrderBy('a.id', Criteria::DESC);

        return $this->fetchList($qb, $this->formatItem(...));
    }

    private function formatItem(Record $item): array
    {
        $event = new RecordFormatEvent();
        $event->setRecord($item);
        $event->setResult($this->normalizer->normalize($item, 'array', ['groups' => 'restful_read']));
        $this->eventDispatcher->dispatch($event);

        return $event->getResult();
    }
}
