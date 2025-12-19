<?php

declare(strict_types=1);

namespace DiyFormBundle\Procedure\Form;

use Carbon\CarbonImmutable;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Param\Form\GetDiyFormDetailParam;
use DiyFormBundle\Repository\FormRepository;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPC\Core\Result\ArrayResult;

#[MethodTag(name: '动态表单')]
#[MethodDoc(summary: '获取单个表单的详细信息')]
#[MethodExpose(method: 'GetDiyFormDetail')]
class GetDiyFormDetail extends BaseProcedure
{
    public function __construct(private readonly FormRepository $formRepository)
    {
    }

    /**
     * @phpstan-param GetDiyFormDetailParam $param
     */
    public function execute(GetDiyFormDetailParam|RpcParamInterface $param): ArrayResult
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

        return $form->retrieveApiArray();
    }
}
