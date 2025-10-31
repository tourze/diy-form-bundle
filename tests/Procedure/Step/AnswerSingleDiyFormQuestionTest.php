<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Procedure\Step;

use DiyFormBundle\Procedure\Step\AnswerSingleDiyFormQuestion;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(AnswerSingleDiyFormQuestion::class)]
#[RunTestsInSeparateProcesses]
final class AnswerSingleDiyFormQuestionTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(AnswerSingleDiyFormQuestion::class);
        $this->assertInstanceOf(AnswerSingleDiyFormQuestion::class, $procedure);
    }

    public function testExecuteMethodExists(): void
    {
        $procedure = self::getService(AnswerSingleDiyFormQuestion::class);
        $this->assertTrue(method_exists($procedure, 'execute'));
    }
}
