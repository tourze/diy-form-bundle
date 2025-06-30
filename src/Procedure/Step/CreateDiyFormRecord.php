<?php

namespace DiyFormBundle\Procedure\Step;

use Carbon\CarbonImmutable;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Repository\FormRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodDoc(summary: '消费者开始答题前，需要先创一个答题记录')]
#[MethodTag(name: '动态表单')]
#[MethodExpose(method: 'CreateDiyFormRecord')]
#[Log]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class CreateDiyFormRecord extends LockableProcedure
{
    #[MethodParam(description: '表单ID')]
    public int $formId = 2;

    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly Security $security,
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

        $record = new Record();
        $record->setForm($form);
        $record->setUser($this->security->getUser());
        $record->setFinished(false);
        $record->setStartTime(CarbonImmutable::now());
        $this->entityManager->persist($record);
        $this->entityManager->flush();

        return $this->normalizer->normalize($record, 'array', ['groups' => 'restful_read']);
    }
}
