<?php

declare(strict_types=1);

namespace DiyFormBundle\ExpressionLanguage;

use DiyFormBundle\Entity\Record;
use DiyFormBundle\ExpressionLanguage\Function\AnswerDiffInMonthFunction;
use DiyFormBundle\ExpressionLanguage\Function\AnswerItemCountFunction;
use DiyFormBundle\ExpressionLanguage\Function\AnswerItemFunction;
use DiyFormBundle\ExpressionLanguage\Function\AnswerTagIncludeFunction;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

#[AutoconfigureTag(name: 'ecol.function.provider')]
#[WithMonologChannel(channel: 'diy_form')]
class DiyFormFunctionProvider implements ExpressionFunctionProviderInterface
{
    private ?Record $record = null;

    /**
     * @var array<string, mixed>
     */
    private array $answerTags = [];

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * @param array<string, mixed> $answerTags
     */
    public function setContext(Record $record, array $answerTags): void
    {
        $this->record = $record;
        $this->answerTags = $answerTags;
    }

    public function getFunctions(): array
    {
        if (null === $this->record) {
            return [];
        }

        $functions = [];

        $func = new AnswerItemFunction($this->logger);
        $func->setRecord($this->record);
        $functions[] = $func;

        $func = new AnswerItemFunction($this->logger, '获取指定题号的回答提交');
        $func->setRecord($this->record);
        $functions[] = $func;

        $func = new AnswerDiffInMonthFunction($this->logger);
        $func->setRecord($this->record);
        $functions[] = $func;

        $func = new AnswerDiffInMonthFunction($this->logger, '计算指定题号回答跟当前时间的月份差');
        $func->setRecord($this->record);
        $functions[] = $func;

        $func = new AnswerItemCountFunction($this->logger);
        $func->setRecord($this->record);
        $functions[] = $func;

        $func = new AnswerItemCountFunction($this->logger, '获取指定题号的回答数量');
        $func->setRecord($this->record);
        $functions[] = $func;

        $func = new AnswerTagIncludeFunction($this->logger, array_keys($this->answerTags));
        $func->setRecord($this->record);
        $functions[] = $func;

        return $functions;
    }
}
