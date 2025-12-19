<?php

declare(strict_types=1);

namespace DiyFormBundle\Param\Record;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class SubmitDiyFormFullRecordParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '表单ID')]
        public string $formId = '',

        /** @var array<string, mixed> */
        #[MethodParam(description: '提交数据')]
        public array $data = [],

        #[MethodParam(description: '开始答题时间')]
        public ?string $startTime = null,

        #[MethodParam(description: '邀请人信息')]
        public ?string $inviter = null,
    ) {
    }
}
