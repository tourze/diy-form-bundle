<?php

namespace DiyFormBundle\Event;

use DiyFormBundle\Entity\Analyse;
use Symfony\Contracts\EventDispatcher\Event;

class RecordAnalyseTriggerEvent extends Event
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

    private Analyse $analyse;

    public function getAnalyse(): Analyse
    {
        return $this->analyse;
    }

    public function setAnalyse(Analyse $analyse): void
    {
        $this->analyse = $analyse;
    }
}
