<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Service;

use DiyFormBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试基类要求的初始化方法
    }

    #[Test]
    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $this->assertInstanceOf(AttributeControllerLoader::class, $service);
    }

    #[Test]
    public function testLoadDelegatesToAutoload(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $result = $service->load('dummy_resource');

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    #[Test]
    public function testSupportsOnlyAttributeType(): void
    {
        $service = self::getService(AttributeControllerLoader::class);

        $this->assertTrue($service->supports('any_resource', 'attribute'));
        $this->assertFalse($service->supports('any_resource', 'annotation'));
        $this->assertFalse($service->supports('any_resource', null));
    }

    #[Test]
    public function testAutoloadReturnsRouteCollection(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $result = $service->autoload();

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    #[Test]
    public function testAutoloadCollectsRoutesFromBothControllers(): void
    {
        $service = self::getService(AttributeControllerLoader::class);
        $collection = $service->autoload();

        // 验证路由数量 > 0 (至少加载了两个控制器的路由)
        $this->assertGreaterThan(0, $collection->count(), '应该收集到路由定义');

        // 验证具体路由是否存在
        $this->assertNotNull($collection->get('diy-model-sql'), 'SqlController 的路由应该被加载');
    }
}
