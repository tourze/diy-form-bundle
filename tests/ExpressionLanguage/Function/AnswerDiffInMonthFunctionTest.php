<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\ExpressionLanguage\Function;

use DiyFormBundle\Entity\Record;
use DiyFormBundle\ExpressionLanguage\Function\AnswerDiffInMonthFunction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(AnswerDiffInMonthFunction::class)]
#[RunTestsInSeparateProcesses]
final class AnswerDiffInMonthFunctionTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testCompiler(): void
    {
        $function = self::getService(AnswerDiffInMonthFunction::class);

        $result = $function->compiler('4');
        $this->assertEquals('answerDiffInMonth(4)', $result);

        $result = $function->compiler(['4', '5']);
        $this->assertEquals("answerDiffInMonth(['4', '5'])", $result);
    }

    public function testEvaluator(): void
    {
        // Mock Record 实体用于测试日期差值计算函数
        $record = $this->createMock(Record::class);

        $function = self::getService(AnswerDiffInMonthFunction::class);
        $function->setRecord($record);

        $record->expects($this->once())
            ->method('obtainInputBySN')
            ->with('4')
            ->willReturn(null)
        ;

        $result = $function->evaluator([], '4');
        $this->assertEquals(0, $result);
    }
}
