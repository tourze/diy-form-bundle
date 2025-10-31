<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Service;

use DiyFormBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Routing\Loader\AttributeClassLoader;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractWebTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试基类要求的初始化方法
    }

    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $this->assertInstanceOf(AttributeControllerLoader::class, $service);
    }

    public function testAutoloadReturnsRouteCollection(): void
    {
        $mockLoader = $this->createMock(AttributeClassLoader::class);
        $mockLoader->method('load')->willReturn(new RouteCollection());

        // 将 mock 服务设置到容器中
        self::getContainer()->set(AttributeClassLoader::class, $mockLoader);
        $service = self::getService(AttributeControllerLoader::class);
        $result = $service->autoload();

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testGetMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $client->request('GET', '/diy-form-bundle');
    }

    #[Test]
    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        if ('INVALID' === $method) {
            // 对于无效方法，我们期望路由系统抛出 NotFoundHttpException
            $client = self::createClientWithDatabase();
            $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
            $client->request($method, '/diy-form-bundle');
            return;
        }

        $client = self::createClientWithDatabase();
        $client->request($method, '/diy-form-bundle');
        $this->assertResponseStatusCodeSame(405);
    }
}
