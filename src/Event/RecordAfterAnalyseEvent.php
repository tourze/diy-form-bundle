<?php

namespace DiyFormBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * 结束分析记录时触发
 */
class RecordAfterAnalyseEvent extends Event
{
    use RecordAware;

    private array $result = [];

    public function getResult(): array
    {
        return $this->result;
    }

    public function setResult(array $result): void
    {
        $this->result = $result;
    }
}
