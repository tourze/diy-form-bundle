<?php

namespace DiyFormBundle\ExpressionLanguage\Function;

use DiyFormBundle\Entity\Record;
use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * 获取指定记录指定题号的回答
 * answerItem('2') 代表读取题目序号等于 2 的题目的答案
 */
class AnswerItemFunction extends ExpressionFunction
{
    protected Record $record;

    public function __construct(private readonly LoggerInterface $logger, string $name = 'answerItem')
    {
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

    public function compiler(string|int $number): string
    {
        return "answerItem({$number})";
    }

    public function evaluator($arguments, string|int $number): mixed
    {
        $number = strval($number);

        $this->logger->debug("answerItem {$number} 判断", [
            'number' => $number,
            'record' => $this->getRecord(),
        ]);
        $res = $this->getRecord()->obtainDataBySN($number)->getInputArray();
        sort($res);

        return implode(',', $res);
    }
}
