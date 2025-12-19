<?php

declare(strict_types=1);

namespace DiyFormBundle\Event;

use Tourze\UserEventBundle\Event\UserInteractionEvent;

/**
 * 提交完整的表单数据时触发.
 */
final class SubmitDiyFormFullRecordEvent extends UserInteractionEvent
{
    use RecordAware;

    /**
     * @var array<string, mixed>
     */
    private array $jsonRpcResult = [];

    /**
     * @return array<string, mixed>
     */
    public function getJsonRpcResult(): array
    {
        return $this->jsonRpcResult;
    }

    /**
     * @param array<string, mixed> $jsonRpcResult
     */
    public function setJsonRpcResult(array $jsonRpcResult): void
    {
        $this->jsonRpcResult = $jsonRpcResult;
    }
}
