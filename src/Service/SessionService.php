<?php

declare(strict_types=1);

namespace DiyFormBundle\Service;

use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Option;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Session\NextField;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

/**
 * @see https://symfony.com/doc/current/components/expression_language/extending.html#registering-functions
 */
#[WithMonologChannel(channel: 'diy_form')]
readonly class SessionService
{
    public function __construct(
        private LoggerInterface $logger,
        private ExpressionEngineService $expressionService,
        private TagCalculator $tagCalculator,
    ) {
    }

    /**
     * 获取下一题答题内容.
     */
    public function getNextField(Record $record): ?NextField
    {
        $answerTagsList = $this->tagCalculator->findByRecord($record);
        // 将 array<string> 转换为 array<string, mixed>
        $answerTags = array_fill_keys($answerTagsList, true);

        $nextField = $this->findNextField($record, $answerTags);

        if (null !== $nextField) {
            $this->processFieldOptions($nextField, $record, $answerTags);
        }

        return $nextField;
    }

    /**
     * @param array<string, mixed> $answerTags
     */
    private function findNextField(Record $record, array $answerTags): ?NextField
    {
        $form = $record->getForm();
        if (null === $form) {
            return null;
        }

        foreach ($form->getSortedFields() as $index => $field) {
            if ($this->shouldSkipField($field, $record, $answerTags)) {
                continue;
            }

            return $this->createNextField($field, $index, $answerTags);
        }

        return null;
    }

    /**
     * @param array<string, mixed> $answerTags
     */
    private function shouldSkipField(Field $field, Record $record, array $answerTags): bool
    {
        if ($record->checkHasAnswered($field)) {
            $this->logger->debug("已经回答过{$field->getSn()}，跳过", [
                'field' => $field,
                'record' => $record,
            ]);

            return true;
        }

        return $this->shouldSkipByExpression($field, $record, $answerTags);
    }

    /**
     * @param array<string, mixed> $answerTags
     */
    private function shouldSkipByExpression(Field $field, Record $record, array $answerTags): bool
    {
        if (null === $field->getShowExpression() || '' === $field->getShowExpression()) {
            return false;
        }

        $this->logger->debug('执行字段表达式', [
            'expression' => $field->getShowExpression(),
            'record' => $record,
            'answerTags' => $answerTags,
        ]);

        $res = $this->expressionService->evaluateWithRecord($field->getShowExpression(), $record, $answerTags);

        if (false === $res) {
            $this->logger->debug("不满足{$field->getSn()}的题目条件，跳过", [
                'field' => $field,
                'record' => $record,
            ]);

            return true;
        }

        return false;
    }

    /**
     * @param array<string, mixed> $answerTags
     */
    private function createNextField(Field $field, int $index, array $answerTags): NextField
    {
        $nextField = new NextField();
        $nextField->setField($field);
        $nextField->setIndex($index);
        $nextField->setShowBack($index > 0);
        $nextField->setAnswerTags(array_map('strval', array_keys($answerTags)));

        return $nextField;
    }

    /**
     * @param array<string, mixed> $answerTags
     */
    private function processFieldOptions(NextField $nextField, Record $record, array $answerTags): void
    {
        if (0 === $nextField->getField()->getOptions()->count()) {
            return;
        }

        foreach ($nextField->getField()->getOptions() as $option) {
            if ($this->shouldIncludeOption($option, $record, $answerTags)) {
                $nextField->addOption($option);
            }
        }
    }

    /**
     * @param array<string, mixed> $answerTags
     */
    private function shouldIncludeOption(Option $option, Record $record, array $answerTags): bool
    {
        if (null === $option->getShowExpression() || '' === $option->getShowExpression()) {
            return true;
        }

        $this->logger->debug('执行选项表达式', [
            'expression' => $option->getShowExpression(),
            'record' => $record,
            'answerTags' => $answerTags,
        ]);

        $res = $this->expressionService->evaluateWithRecord($option->getShowExpression(), $record, $answerTags);

        if (false === $res) {
            $this->logger->debug("选项{$option->getId()}不满足显示条件，跳过", [
                'option' => $option,
                'record' => $record,
            ]);

            return false;
        }

        return true;
    }
}
