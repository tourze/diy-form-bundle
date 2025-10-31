<?php

declare(strict_types=1);

namespace DiyFormBundle\Event;

use DiyFormBundle\Entity\Option;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * 有一些情景，我们需要额外插入特殊的选项，可以在这里进行.
 */
class OptionsFormatEvent extends Event
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

    /**
     * @var array<int, Option>
     */
    private array $options;

    /**
     * @return array<int, Option>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array<int, Option> $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }
}
