<?php

declare(strict_types=1);

namespace DiyFormBundle\Service;

use DiyFormBundle\Entity\Record;
use DiyFormBundle\ExpressionLanguage\DiyFormFunctionProvider;
use Tourze\EcolBundle\Service\Engine;

/**
 * 基于 Engine 的表达式服务
 */
readonly class ExpressionEngineService
{
    public function __construct(
        private Engine $engine,
        private DiyFormFunctionProvider $functionProvider,
    ) {
    }

    /**
     * @param array<string, mixed> $answerTags
     * @param array<string, mixed> $values
     */
    public function evaluateWithRecord(string $expression, Record $record, array $answerTags, array $values = []): mixed
    {
        $this->functionProvider->setContext($record, $answerTags);

        $defaultValues = [
            'form' => $record->getForm(),
            'record' => $record,
        ];

        return $this->engine->evaluate($expression, array_merge($defaultValues, $values));
    }
}
