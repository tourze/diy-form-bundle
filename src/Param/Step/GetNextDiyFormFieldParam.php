<?php

declare(strict_types=1);

namespace DiyFormBundle\Param\Step;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class GetNextDiyFormFieldParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '表单ID')]
        public int $formId = 2,

        #[MethodParam(description: '记录ID')]
        public int $recordId = 0,
    ) {
    }
}
