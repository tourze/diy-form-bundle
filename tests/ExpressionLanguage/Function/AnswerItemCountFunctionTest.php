<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\ExpressionLanguage\Function;

use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\ExpressionLanguage\Function\AnswerItemCountFunction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(AnswerItemCountFunction::class)]
#[RunTestsInSeparateProcesses]
final class AnswerItemCountFunctionTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testCompiler(): void
    {
        $function = self::getService(AnswerItemCountFunction::class);

        $result = $function->compiler('2');
        $this->assertEquals('answerItemCount(2)', $result);

        $result = $function->compiler(3);
        $this->assertEquals('answerItemCount(3)', $result);
    }

    public function testEvaluator(): void
    {
        // Mock Data 实体用于测试获取输入数组并计算项目数量
        $data = $this->createMock(Data::class);
        $data->expects($this->once())
            ->method('getInputArray')
            ->willReturn(['选项A', '选项B', '选项C'])
        ;

        // Mock Record 实体用于测试根据 SN 获取数据
        $record = $this->createMock(Record::class);
        $record->expects($this->once())
            ->method('obtainDataBySN')
            ->with('2')
            ->willReturn($data)
        ;

        $function = self::getService(AnswerItemCountFunction::class);
        $function->setRecord($record);

        $result = $function->evaluator([], '2');
        $this->assertEquals(3, $result);
    }

    public function testEvaluatorWithNullData(): void
    {
        // Mock Record 实体用于测试数据为空的情况
        $record = $this->createMock(Record::class);
        $record->expects($this->once())
            ->method('obtainDataBySN')
            ->with('2')
            ->willReturn(null)
        ;

        $function = self::getService(AnswerItemCountFunction::class);
        $function->setRecord($record);

        $result = $function->evaluator([], '2');
        $this->assertEquals(0, $result);
    }

    public function testEvaluatorWithSpecialOption(): void
    {
        // Mock Data 实体用于测试特殊选项的处理
        $data = $this->createMock(Data::class);
        $data->expects($this->once())
            ->method('getInputArray')
            ->willReturn(['选项A', '选项B', '以上均无'])
        ;

        // Mock Record 实体用于测试特殊选项场景
        $record = $this->createMock(Record::class);
        $record->expects($this->once())
            ->method('obtainDataBySN')
            ->with('2')
            ->willReturn($data)
        ;

        $function = self::getService(AnswerItemCountFunction::class);
        $function->setRecord($record);

        $result = $function->evaluator([], '2');
        $this->assertEquals(2, $result); // '以上均无' 被排除
    }
}
