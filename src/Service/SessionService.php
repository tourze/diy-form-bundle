<?php

namespace DiyFormBundle\Service;

use DiyFormBundle\Entity\Record;
use DiyFormBundle\Session\NextField;
use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @see https://symfony.com/doc/current/components/expression_language/extending.html#registering-functions
 */
class SessionService
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ExpressionService $expressionService,
        private readonly TagCalculator $tagCalculator,
    ) {
    }

    /**
     * 获取下一题答题内容
     */
    public function getNextField(Record $record): ?NextField
    {
        $expressionLanguage = new ExpressionLanguage();
        $answerTags = $this->tagCalculator->findByRecord($record);
        $this->expressionService->bindRecordFunction($expressionLanguage, $record, $answerTags);

        $expressionValues = [
            'form' => $record->getForm(),
            'record' => $record,
        ];

        $nextField = null;
        foreach ($record->getForm()->getSortedFields() as $index => $field) {
            // 如果这一题填过了，那就不能重复填
            if ($record->checkHasAnswered($field)) {
                $this->logger->debug("已经回答过{$field->getSn()}，跳过", [
                    'field' => $field,
                    'record' => $record,
                ]);
                continue;
            }

            // 如果有显示条件的话，我们看当前用户是否满足显示条件
            if (!empty($field->getShowExpression())) {
                $this->logger->debug('执行字段表达式', [
                    'expression' => $field->getShowExpression(),
                    'values' => $expressionValues,
                ]);
                $res = $expressionLanguage->evaluate($field->getShowExpression(), $expressionValues);
                // 不满足的话，我们就跳过这个题目
                if (!$res) {
                    $this->logger->debug("不满足{$field->getSn()}的题目条件，跳过", [
                        'field' => $field,
                        'record' => $record,
                    ]);
                    continue;
                }
            }

            $nextField = new NextField();
            $nextField->setField($field);
            $nextField->setIndex($index);
            $nextField->setShowBack($index > 0); // 默认情况下，只有第一题不显示返回上一题
            $nextField->setAnswerTags($answerTags);
            break;
        }

        // 最终送出去的题目，选项需要多判断一次才能用
        if ($nextField && $nextField->getField()->getOptions()->count() > 0) {
            foreach ($nextField->getField()->getOptions() as $option) {
                if (empty($option->getShowExpression())) {
                    $nextField->addOption($option);
                    continue;
                }

                $this->logger->debug('执行选项表达式', [
                    'expression' => $option->getShowExpression(),
                    'values' => $expressionValues,
                ]);
                $res = $expressionLanguage->evaluate($option->getShowExpression(), $expressionValues);
                if (!$res) {
                    $this->logger->debug("选项{$option->getId()}不满足显示条件，跳过", [
                        'option' => $option,
                        'record' => $record,
                    ]);
                    continue;
                }

                $nextField->addOption($option);
            }
        }

        return $nextField;
    }
}
