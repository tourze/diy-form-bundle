<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\ExpressionLanguage\Function;

use DiyFormBundle\Entity\Record;
use DiyFormBundle\ExpressionLanguage\Function\AnswerTagIncludeFunction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(AnswerTagIncludeFunction::class)]
#[RunTestsInSeparateProcesses]
final class AnswerTagIncludeFunctionTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testCompiler(): void
    {
        $function = self::getService(AnswerTagIncludeFunction::class);

        $result = $function->compiler('猫');
        $this->assertEquals("answerTagInclude('猫')", $result);

        $result = $function->compiler('狗');
        $this->assertEquals("answerTagInclude('狗')", $result);
    }

    public function testEvaluator(): void
    {
        $answerTags = ['猫', '宠物', '可爱'];
        $function = self::getService(AnswerTagIncludeFunction::class);
        $function->setAnswerTags($answerTags);

        /*
         * 使用具体类 Record 进行 mock 的原因：
         * 1. Record 是一个 Doctrine 实体类，没有对应的接口
         * 2. 表达式函数需要设置 Record 对象作为上下文
         * 3. 这是测试表达式函数的标准做法，确保函数能正确处理实体对象
         */
        $record = $this->createMock(Record::class);
        $function->setRecord($record);

        $result = $function->evaluator([], '猫');
        $this->assertTrue($result);

        $result = $function->evaluator([], '狗');
        $this->assertFalse($result);
    }
}
