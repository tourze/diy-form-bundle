<?php

namespace DiyFormBundle\ExpressionLanguage\Function;

use DiyFormBundle\Entity\Record;
use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * 判断当前记录是否包含了指定的标签
 * answerTagInclude('猫') 代表已经打上了猫的标签
 */
class AnswerTagIncludeFunction extends ExpressionFunction
{
    protected Record $record;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly array $answerTags = [],
        string $name = 'answerTagInclude',
    ) {
        parent::__construct($name, $this->compiler(...), $this->evaluator(...));
    }

    public function getRecord(): Record
    {
        return $this->record;
    }

    public function setRecord(Record $record): void
    {
        $this->record = $record;
    }

    public function compiler(string $number): string
    {
        return "answerTagInclude('{$number}')";
    }

    public function evaluator($arguments, string $tagName): bool
    {
        $this->logger->debug(sprintf('answerTagInclude %s 判断', $tagName), [
            'tagName' => $tagName,
            'answerTags' => $this->answerTags,
            'record' => $this->getRecord(),
        ]);

        return in_array($tagName, $this->answerTags);
    }
}
