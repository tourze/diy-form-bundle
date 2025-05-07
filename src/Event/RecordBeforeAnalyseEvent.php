<?php

namespace DiyFormBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * 开始分析记录时触发
 */
class RecordBeforeAnalyseEvent extends Event
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
