<?php

declare(strict_types=1);

namespace DiyFormBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class FieldFormatEvent extends Event
{
    use RecordAware;
    use FieldAware;

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
}
