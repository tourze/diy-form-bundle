<?php

namespace DiyFormBundle\ExpressionLanguage\Function;

use DiyFormBundle\Entity\Record;
use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * 获取指定记录指定题号的回答数量
 * 例如第2题是多选题，那么 answerItemCount('2') 代表读取第2题的选中数量
 */
class AnswerItemCountFunction extends ExpressionFunction
{
    protected Record $record;

    public function __construct(private readonly LoggerInterface $logger, string $name = 'answerItemCount')
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
        return "answerItemCount({$number})";
    }

    public function evaluator($arguments, string|int $number): int
    {
        $number = strval($number);
        $data = $this->getRecord()->obtainDataBySN($number);
        $this->logger->debug("answerItemCount {$number} 判断", [
            'number' => $number,
            'record' => $this->getRecord(),
            'data' => $data,
        ]);

        if (null === $data) {
            return 0;
        }

        $arr = $data->getInputArray();
        foreach ($arr as $k => $v) {
            if ('以上均无' === $v) {
                // TODO 改用更加优雅的方式
                unset($arr[$k]);
            }
        }

        return count($arr);
    }
}
