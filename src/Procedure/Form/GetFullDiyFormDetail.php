<?php

declare(strict_types=1);

namespace DiyFormBundle\Procedure\Form;

use Carbon\CarbonImmutable;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Param\Form\GetFullDiyFormDetailParam;
use DiyFormBundle\Repository\FormRepository;
use DiyFormBundle\Repository\RecordRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPC\Core\Result\ArrayResult;

#[MethodTag(name: '动态表单')]
#[MethodDoc(summary: '获取单个表单的完整信息')]
#[MethodExpose(method: 'GetFullDiyFormDetail')]
class GetFullDiyFormDetail extends BaseProcedure
{
    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly RecordRepository $recordRepository,
        private readonly Security $security,
    ) {
    }

    /**
     * @phpstan-param GetFullDiyFormDetailParam $param
     */
    public function execute(GetFullDiyFormDetailParam|RpcParamInterface $param): ArrayResult
    {
        $form = $this->formRepository->findOneBy([
            'id' => $param->formId,
            'valid' => true,
        ]);
        if (!$form instanceof Form) {
            throw new ApiException('找不到表单配置');
        }

        $now = CarbonImmutable::now();
        $startTime = $form->getStartTime();
        $endTime = $form->getEndTime();

        if (null !== $startTime && $now->lessThan($startTime)) {
            throw new ApiException('该表单还未开始');
        }
        if (null !== $endTime && $now->greaterThan($endTime)) {
            throw new ApiException('该表单已结束');
        }

        $result = $form->retrievePlainArray();
        // 使用独立数组收集字段
        /** @var array<int, array<string, mixed>> $fields */
        $fields = [];
        foreach ($form->getSortedFields() as $sortedField) {
            $fields[] = $sortedField->retrievePlainArray();
        }
        $result['fields'] = $fields;

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

        return new ArrayResult($result);
    }
}
