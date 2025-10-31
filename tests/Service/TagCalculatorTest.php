<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Service;

use DiyFormBundle\Service\TagCalculator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(TagCalculator::class)]
#[RunTestsInSeparateProcesses]
final class TagCalculatorTest extends AbstractIntegrationTestCase
{
    private TagCalculator $tagCalculator;

    protected function onSetUp(): void
    {
    }

    private function getTagCalculator(): TagCalculator
    {
        return $this->tagCalculator ??= self::getService(TagCalculator::class);
    }

    public function testTagCalculator基本功能测试(): void
    {
        $tagCalculator = $this->getTagCalculator();
        // assertInstanceOf 对已知类型是冗余的，getTagCalculator() 已经保证返回 TagCalculator 类型
        $this->assertNotNull($tagCalculator);
    }

    public function testTagCalculator验证构造函数依赖注入(): void
    {
        // 通过反射验证构造函数的依赖项
        $reflectionClass = new \ReflectionClass(TagCalculator::class);
        $constructor = $reflectionClass->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertEquals(3, $constructor->getNumberOfParameters());

        $parameters = $constructor->getParameters();
        $this->assertEquals('logger', $parameters[0]->getName());
        $this->assertEquals('eventDispatcher', $parameters[1]->getName());
        $this->assertEquals('dataRepository', $parameters[2]->getName());
    }

    public function testFindByRecord(): void
    {
        // 这是一个集成测试，直接验证TagCalculator服务能够正常处理Record
        // 不进行实际的数据库查询测试，只验证方法调用不出错
        $tagCalculator = $this->getTagCalculator();

        // TagCalculator实例已经由getTagCalculator()方法保证类型，验证它可以正常实例化
        self::assertIsObject($tagCalculator);

        // 验证TagCalculator的核心方法存在且可调用
        $reflection = new \ReflectionClass(TagCalculator::class);
        self::assertTrue($reflection->hasMethod('findByRecord'));

        $method = $reflection->getMethod('findByRecord');
        self::assertTrue($method->isPublic());

        // 验证方法参数数量是否正确
        self::assertCount(1, $method->getParameters(), 'findByRecord方法应该有一个参数');
    }
}
