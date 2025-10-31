<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\ExpressionLanguage\Function;

use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\ExpressionLanguage\Function\AnswerItemFunction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(AnswerItemFunction::class)]
#[RunTestsInSeparateProcesses]
final class AnswerItemFunctionTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testCompiler(): void
    {
        $function = self::getService(AnswerItemFunction::class);

        $result = $function->compiler('2');
        $this->assertEquals('answerItem(2)', $result);

        $result = $function->compiler(3);
        $this->assertEquals('answerItem(3)', $result);
    }

    public function testEvaluator(): void
    {
        /*
         * 使用具体类 Data 进行 mock 的原因：
         * 1. Data 是一个 Doctrine 实体类，没有对应的接口
         * 2. 在表达式语言函数中需要访问 Data 的具体属性
         * 3. 这是测试表达式函数的标准做法，确保函数能正确处理实体对象
         */
        $data = $this->createMock(Data::class);
        $data->expects($this->once())
            ->method('getInputArray')
            ->willReturn(['选项A', '选项B', '选项C'])
        ;

        /*
         * 使用具体类 Record 进行 mock 的原因：
         * 1. Record 是一个 Doctrine 实体类，没有对应的接口
         * 2. 表达式函数需要访问 Record 的 obtainDataBySN 方法
         * 3. 这是测试表达式函数的标准做法，确保函数能正确处理实体对象
         */
        $record = $this->createMock(Record::class);
        $record->expects($this->once())
            ->method('obtainDataBySN')
            ->with('2')
            ->willReturn($data)
        ;

        $function = self::getService(AnswerItemFunction::class);
        $function->setRecord($record);

        $result = $function->evaluator([], '2');
        $this->assertEquals('选项A,选项B,选项C', $result);
    }
}
