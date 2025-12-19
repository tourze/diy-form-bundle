<?php

declare(strict_types=1);

namespace DiyFormBundle\Param\Step;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class CreateDiyFormRecordParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '表单ID')]
        public int $formId = 2,
    ) {
    }
}
