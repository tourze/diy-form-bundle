<?php

namespace DiyFormBundle\ExpressionLanguage\Function;

use Carbon\Carbon;
use DiyFormBundle\Entity\Record;
use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * 计算跟当前时间的月份差
 * answerDiffInMonth('4') 代表第4题跟现在的月份对比，差的月份是多少
 */
class AnswerDiffInMonthFunction extends ExpressionFunction
{
    protected Record $record;

    public function __construct(private readonly LoggerInterface $logger, string $name = 'answerDiffInMonth')
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

    public function compiler(string|int|array $number): string
    {
        if (is_array($number)) {
            $number = implode("', '", $number);
            $number = "['{$number}']";
        }

        return "answerDiffInMonth({$number})";
    }

    public function evaluator($arguments, string|int|array $number): mixed
    {
        if (!is_array($number)) {
            $number = [$number];
        }

        foreach ($number as $item) {
            $item = strval($item);

            $this->logger->debug("answerDiffInMonth {$item} 判断", [
                'item' => $item,
                'record' => $this->getRecord(),
            ]);
            $day = $this->getRecord()->obtainInputBySN($item);
            if (null === $day) {
                continue;
            }

            return Carbon::now()->diffInMonths(Carbon::parse($day));
        }

        return 0;
    }
}
