<?php

namespace DiyFormBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class RecordFormatEvent extends Event
{
    private array $result = [];

    public function getResult(): array
    {
        return $this->result;
    }

    public function setResult(array $result): void
    {
        $this->result = $result;
    }

    use RecordAware;
}
