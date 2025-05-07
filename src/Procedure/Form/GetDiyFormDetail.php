<?php

namespace DiyFormBundle\Procedure\Form;

use Carbon\Carbon;
use DiyFormBundle\Repository\FormRepository;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag('动态表单')]
#[MethodDoc('获取单个表单的详细信息')]
#[MethodExpose('GetDiyFormDetail')]
class GetDiyFormDetail extends BaseProcedure
{
    #[MethodParam('表单ID')]
    public string $formId = '2';

    public function __construct(private readonly FormRepository $formRepository)
    {
    }

    public function execute(): array
    {
        $form = $this->formRepository->findOneBy([
            'id' => $this->formId,
            'valid' => true,
        ]);
        if (!$form) {
            throw new ApiException('找不到表单配置');
        }
        if ($form->getStartTime()->format('Y-m-d H:i:s') > Carbon::parse()->format('Y-m-d H:i:s')) {
            throw new ApiException('该表单还未开始');
        }
        if ($form->getEndTime()->format('Y-m-d H:i:s') < Carbon::parse()->format('Y-m-d H:i:s')) {
            throw new ApiException('该表单已结束');
        }

        return $form->retrieveApiArray();
    }
}
