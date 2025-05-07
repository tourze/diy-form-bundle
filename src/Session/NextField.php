<?php

namespace DiyFormBundle\Session;

use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Option;

class NextField
{
    /**
     * @var int 记录当前题目排序的索引
     */
    protected int $index;

    protected bool $showBack = true;

    private Field $field;

    /**
     * @var Option[] 最终可以选择的选项
     */
    private array $options = [];

    private array $answerTags = [];

    public function getField(): Field
    {
        return $this->field;
    }

    public function setField(Field $field): void
    {
        $this->field = $field;
    }

    /**
     * @return Option[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param Option[] $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function addOption(Option $option): void
    {
        $this->options[] = $option;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function setIndex(int $index): void
    {
        $this->index = $index;
    }

    public function isShowBack(): bool
    {
        return $this->showBack;
    }

    public function setShowBack(bool $showBack): void
    {
        $this->showBack = $showBack;
    }

    public function getAnswerTags(): array
    {
        return $this->answerTags;
    }

    public function setAnswerTags(array $answerTags): void
    {
        $this->answerTags = $answerTags;
    }
}
