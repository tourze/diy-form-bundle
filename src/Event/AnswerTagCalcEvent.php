<?php

namespace DiyFormBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AnswerTagCalcEvent extends Event
{
    use RecordAware;

    private array $answerTags;

    public function getAnswerTags(): array
    {
        return $this->answerTags;
    }

    public function setAnswerTags(array $answerTags): void
    {
        $this->answerTags = $answerTags;
    }
}
