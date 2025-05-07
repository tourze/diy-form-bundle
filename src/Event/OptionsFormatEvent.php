<?php

namespace DiyFormBundle\Event;

use DiyFormBundle\Entity\Option;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * 有一些情景，我们需要额外插入特殊的选项，可以在这里进行
 */
class OptionsFormatEvent extends Event
{
    use RecordAware;
    use FieldAware;

    private array $result = [];

    public function getResult(): array
    {
        return $this->result;
    }

    public function setResult(array $result): void
    {
        $this->result = $result;
    }

    /**
     * @var array|Option[]
     */
    private array $options;

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }
}
