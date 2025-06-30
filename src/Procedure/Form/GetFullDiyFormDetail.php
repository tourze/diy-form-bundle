<?php

namespace DiyFormBundle\Procedure\Form;

use Carbon\CarbonImmutable;
use DiyFormBundle\Repository\FormRepository;
use DiyFormBundle\Repository\RecordRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag(name: '动态表单')]
#[MethodDoc(summary: '获取单个表单的完整信息')]
#[MethodExpose(method: 'GetFullDiyFormDetail')]
class GetFullDiyFormDetail extends BaseProcedure
{
    #[MethodParam(description: '表单ID')]
    public string $formId;

    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly RecordRepository $recordRepository,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $form = $this->formRepository->findOneBy([
            'id' => $this->formId,
            'valid' => true,
        ]);
        if (null === $form) {
            throw new ApiException('找不到表单配置');
        }

        $now = CarbonImmutable::now();
        if ($now->lessThan($form->getStartTime())) {
            throw new ApiException('该表单还未开始');
        }
        if ($now->greaterThan($form->getEndTime())) {
            throw new ApiException('该表单已结束');
        }

        $result = $form->retrievePlainArray();
        foreach ($form->getSortedFields() as $sortedField) {
            $result['fields'][] = $sortedField->retrievePlainArray();
        }

        // 上一次的记录
        $result['lastRecord'] = null;
        if (null !== $this->security->getUser()) {
            $record = $this->recordRepository->findOneBy([
                'form' => $form,
                'user' => $this->security->getUser(),
                'finished' => true,
            ], orderBy: ['id' => 'DESC']);
            if (null !== $record) {
                $result['lastRecord'] = [
                    'id' => $record->getId(),
                    'startTime' => $record->getStartTime()?->format('Y-m-d H:i:s'),
                    'finishTime' => $record->getFinishTime()?->format('Y-m-d H:i:s'),
                ];
            }
        }

        return $result;
    }
}
