<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\ExpressionLanguage;

use DiyFormBundle\Entity\Record;
use DiyFormBundle\ExpressionLanguage\DiyFormFunctionProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(DiyFormFunctionProvider::class)]
#[RunTestsInSeparateProcesses]
final class DiyFormFunctionProviderTest extends AbstractIntegrationTestCase
{
    private DiyFormFunctionProvider $provider;

    protected function onSetUp(): void
    {
    }

    private function getProvider(): DiyFormFunctionProvider
    {
        return $this->provider ??= self::getService(DiyFormFunctionProvider::class);
    }

    public function testGetFunctions未设置上下文时返回空数组(): void
    {
        $functions = $this->getProvider()->getFunctions();
        $this->assertIsArray($functions);
        $this->assertEmpty($functions);
    }

    public function testSetContextAndGetFunctions设置上下文后返回正确函数数量(): void
    {
        /*
         * 1. 必须使用具体类 Record::class 而不是接口：需要模拟 Record 实体
         *    来测试表达式函数提供者的上下文设置功能
         * 2. 使用合理性：合理且必要，Record 是表达式上下文的核心数据载体
         * 3. 替代方案：真实 Record 需要完整的数据关联，mock 更适合单元测试
         */
        $record = $this->createMock(Record::class);
        $answerTags = ['tag1' => 'value1'];

        $provider = $this->getProvider();
        $provider->setContext($record, $answerTags);
        $functions = $provider->getFunctions();

        $this->assertIsArray($functions);
        $this->assertCount(7, $functions);
    }

    public function testSetContext正确设置记录和标签(): void
    {
        /*
         * 1. 必须使用具体类 Record::class 而不是接口：需要模拟 Record 实体
         *    来测试表达式上下文的记录和标签设置正确性
         * 2. 使用合理性：合理且必要，Record 是表达式计算的数据基础
         * 3. 替代方案：真实 Record 设置复杂，mock 能精确控制测试数据
         */
        $record = $this->createMock(Record::class);
        $answerTags = ['tag1' => 'value1', 'tag2' => 'value2'];

        $provider = $this->getProvider();
        $provider->setContext($record, $answerTags);
        $functions = $provider->getFunctions();

        // 验证函数被正确创建
        $this->assertNotEmpty($functions);
        foreach ($functions as $function) {
            $this->assertInstanceOf(ExpressionFunction::class, $function);
        }
    }

    public function testGetFunctions创建正确的表达式函数类型(): void
    {
        /*
         * 1. 必须使用具体类 Record::class 而不是接口：需要模拟 Record 实体
         *    来测试表达式函数的创建和类型验证
         * 2. 使用合理性：合理且必要，函数创建需要记录上下文支持
         * 3. 替代方案：真实 Record 需要数据库支持，mock 更适合类型测试
         */
        $record = $this->createMock(Record::class);
        $answerTags = [];

        $provider = $this->getProvider();
        $provider->setContext($record, $answerTags);
        $functions = $provider->getFunctions();

        $functionNames = array_map(fn ($func) => $func->getName(), $functions);

        // 验证包含期望的函数名
        $this->assertContains('answerItem', $functionNames);
        $this->assertContains('获取指定题号的回答提交', $functionNames);
        $this->assertContains('answerDiffInMonth', $functionNames);
        $this->assertContains('计算指定题号回答跟当前时间的月份差', $functionNames);
        $this->assertContains('answerItemCount', $functionNames);
        $this->assertContains('获取指定题号的回答数量', $functionNames);
        $this->assertContains('answerTagInclude', $functionNames);
    }
}
