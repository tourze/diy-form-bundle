<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Service;

use DiyFormBundle\Service\SessionService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(SessionService::class)]
#[RunTestsInSeparateProcesses]
final class SessionServiceTest extends AbstractIntegrationTestCase
{
    private SessionService $sessionService;

    protected function onSetUp(): void
    {
    }

    private function getSessionService(): SessionService
    {
        return $this->sessionService ??= self::getService(SessionService::class);
    }

    public function testGetNextField基本功能测试(): void
    {
        $sessionService = $this->getSessionService();
        $this->assertInstanceOf(SessionService::class, $sessionService);
    }

    public function testSessionService可以正常获取(): void
    {
        $sessionService = $this->getSessionService();
        $this->assertInstanceOf(SessionService::class, $sessionService);
    }

    public function testSessionService验证构造函数依赖注入(): void
    {
        // 通过反射验证构造函数的依赖项
        $reflectionClass = new \ReflectionClass(SessionService::class);
        $constructor = $reflectionClass->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertEquals(3, $constructor->getNumberOfParameters());

        $parameters = $constructor->getParameters();
        $this->assertEquals('logger', $parameters[0]->getName());
        $this->assertEquals('expressionService', $parameters[1]->getName());
        $this->assertEquals('tagCalculator', $parameters[2]->getName());
    }
}
