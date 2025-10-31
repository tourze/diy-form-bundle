<?php

declare(strict_types=1);

namespace DiyFormBundle\Event;

use DiyFormBundle\Entity\Analyse;
use Symfony\Contracts\EventDispatcher\Event;

class RecordAnalyseTriggerEvent extends Event
{
    use RecordAware;

    /**
     * @var array<string, mixed>
     */
    private array $result = [];

    /**
     * @return array<string, mixed>
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @param array<string, mixed> $result
     */
    public function setResult(array $result): void
    {
        $this->result = $result;
    }

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
