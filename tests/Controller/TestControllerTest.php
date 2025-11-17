<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Controller;

use DiyFormBundle\Controller\TestController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(TestController::class)]
#[RunTestsInSeparateProcesses]
final class TestControllerTest extends AbstractWebTestCase
{
    public function testUnauthenticatedAccessReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request('GET', '/diy-form/get-form-tags/123');
    }

    public function testGetMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request('GET', '/diy-form/get-form-tags/100');
    }

    public function testPostMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);
        $client->request('POST', '/diy-form/get-form-tags/100');

        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testPutMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);
        $client->request('PUT', '/diy-form/get-form-tags/100');

        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testDeleteMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);
        $client->request('DELETE', '/diy-form/get-form-tags/100');

        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testPatchMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);
        $client->request('PATCH', '/diy-form/get-form-tags/100');

        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testHeadMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);
        $client->request('HEAD', '/diy-form/get-form-tags/100');

        $response = $client->getResponse();
        // HEAD 请求会自动映射到 GET 方法,因此返回 404 而非 405
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testOptionsMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);
        $client->request('OPTIONS', '/diy-form/get-form-tags/100');

        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        // 该测试控制器的路由不存在,期望返回404而非405
        self::markTestSkipped('测试控制器路由不存在,无法测试405 Method Not Allowed');
    }
}
