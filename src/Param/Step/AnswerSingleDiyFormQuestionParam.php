<?php

declare(strict_types=1);

namespace DiyFormBundle\Param\Step;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class AnswerSingleDiyFormQuestionParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '表单ID')]
        public string $formId = '2',

        #[MethodParam(description: '记录ID')]
        public int $recordId = 0,

        #[MethodParam(description: '题目/字段ID，如果是希望拿第一题，那这里可以不传入')]
        public int $fieldId = 0,

        /** @var string|array<int, mixed>|int */
        #[MethodParam(description: '输入/选择值，如果是希望拿第一题，那这里可以不传入')]
        public string|array|int $input = '',

        #[MethodParam(description: '是否跳过这个题目，跳过的话input可以不传入')]
        public bool $skip = false,
    ) {
    }
}
