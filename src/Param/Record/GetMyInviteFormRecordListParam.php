<?php

declare(strict_types=1);

namespace DiyFormBundle\Param\Record;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPCPaginatorBundle\Param\PaginatorParamInterface;

readonly class GetMyInviteFormRecordListParam implements PaginatorParamInterface
{
    public function __construct(
        #[MethodParam(description: '表单ID')]
        public ?string $formId = null,

        #[MethodParam(description: '当前页数')]
        public int $currentPage = 1,

        #[MethodParam(description: '每页条数')]
        public int $pageSize = 20,

        #[MethodParam(description: '上一次拉取时，最后一条数据的主键ID')]
        public ?int $lastId = null,
    ) {
    }
}
