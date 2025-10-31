<?php

declare(strict_types=1);

namespace DiyFormBundle\ExpressionLanguage\Function;

use DiyFormBundle\Entity\Record;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * 判断当前记录是否包含了指定的标签
 * answerTagInclude('猫') 代表已经打上了猫的标签.
 */
#[WithMonologChannel(channel: 'diy_form')]
class AnswerTagIncludeFunction extends ExpressionFunction
{
    protected Record $record;

    /**
     * @param array<string> $answerTags
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private array $answerTags = [],
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

    /**
     * @param array<string> $answerTags
     */
    public function setAnswerTags(array $answerTags): void
    {
        $this->answerTags = $answerTags;
    }

    public function compiler(string $number): string
    {
        return "answerTagInclude('{$number}')";
    }

    /**
     * @param array<mixed> $arguments
     */
    public function evaluator(array $arguments, string $tagName): bool
    {
        $this->logger->debug(sprintf('answerTagInclude %s 判断', $tagName), [
            'tagName' => $tagName,
            'answerTags' => $this->answerTags,
            'record' => $this->getRecord(),
        ]);

        return in_array($tagName, $this->answerTags, true);
    }
}
