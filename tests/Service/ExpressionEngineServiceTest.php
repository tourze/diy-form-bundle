<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Service;

use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Service\ExpressionEngineService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ExpressionEngineService::class)]
#[RunTestsInSeparateProcesses]
final class ExpressionEngineServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试基类要求的初始化方法
    }

    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(ExpressionEngineService::class);
        $this->assertInstanceOf(ExpressionEngineService::class, $service);
    }

    public function testEvaluateWithRecord(): void
    {
        $service = self::getService(ExpressionEngineService::class);

        // 创建一个Mock的Record对象用于测试
        $record = $this->createMock(Record::class);
        $record->method('getForm')->willReturn($this->createMock(Form::class));

        // 测试简单表达式
        $result = $service->evaluateWithRecord('1 + 1', $record, []);
        $this->assertEquals(2, $result);

        // 测试带有values参数的表达式
        $result = $service->evaluateWithRecord('x + y', $record, [], ['x' => 3, 'y' => 4]);
        $this->assertEquals(7, $result);

        // 测试字符串表达式
        $result = $service->evaluateWithRecord('"hello"', $record, []);
        $this->assertEquals('hello', $result);
    }
}
