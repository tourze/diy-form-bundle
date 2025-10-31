<?php

declare(strict_types=1);

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
     * @var array<int, Option> 最终可以选择的选项
     */
    private array $options = [];

    /**
     * @var array<string>
     */
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

    /**
     * @return array<string>
     */
    public function getAnswerTags(): array
    {
        return $this->answerTags;
    }

    /**
     * @param array<string> $answerTags
     */
    public function setAnswerTags(array $answerTags): void
    {
        $this->answerTags = $answerTags;
    }
}
