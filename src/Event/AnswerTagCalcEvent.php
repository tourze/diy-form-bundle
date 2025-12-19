<?php

declare(strict_types=1);

namespace DiyFormBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class AnswerTagCalcEvent extends Event
{
    use RecordAware;

    /**
     * @var array<string>
     */
    private array $answerTags;

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
