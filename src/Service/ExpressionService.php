<?php

namespace DiyFormBundle\Service;

use DiyFormBundle\Entity\Record;
use DiyFormBundle\ExpressionLanguage\Function\AnswerDiffInMonthFunction;
use DiyFormBundle\ExpressionLanguage\Function\AnswerItemCountFunction;
use DiyFormBundle\ExpressionLanguage\Function\AnswerItemFunction;
use DiyFormBundle\ExpressionLanguage\Function\AnswerTagIncludeFunction;
use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @deprecated 这里的逻辑，合并到 \Tourze\EcolBundle\Service\Engine
 */
class ExpressionService
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function bindRecordFunction(ExpressionLanguage $expressionLanguage, Record $record, array $answerTags): void
    {
        $func = new AnswerItemFunction($this->logger);
        $func->setRecord($record);
        $expressionLanguage->addFunction($func);
        $func = new AnswerItemFunction($this->logger, '获取指定题号的回答提交');
        $func->setRecord($record);
        $expressionLanguage->addFunction($func);

        $func = new AnswerDiffInMonthFunction($this->logger);
        $func->setRecord($record);
        $expressionLanguage->addFunction($func);
        $func = new AnswerDiffInMonthFunction($this->logger, '计算指定题号回答跟当前时间的月份差');
        $func->setRecord($record);
        $expressionLanguage->addFunction($func);

        $func = new AnswerItemCountFunction($this->logger);
        $func->setRecord($record);
        $expressionLanguage->addFunction($func);
        $func = new AnswerItemCountFunction($this->logger, '获取指定题号的回答数量');
        $func->setRecord($record);
        $expressionLanguage->addFunction($func);

        $func = new AnswerTagIncludeFunction($this->logger, $answerTags);
        $func->setRecord($record);
        $expressionLanguage->addFunction($func);

        // TODO 更多方法
    }
}
