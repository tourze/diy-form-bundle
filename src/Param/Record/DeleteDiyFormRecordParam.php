<?php

declare(strict_types=1);

namespace DiyFormBundle\Param\Record;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class DeleteDiyFormRecordParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '记录ID')]
        public string $recordId = '',
    ) {
    }
}
