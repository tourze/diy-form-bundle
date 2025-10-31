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

        $this->expectException(NotFoundHttpException::class);

        $client->request('POST', '/diy-form/get-form-tags/100');
    }

    public function testPutMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request('PUT', '/diy-form/get-form-tags/100');
    }

    public function testDeleteMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request('DELETE', '/diy-form/get-form-tags/100');
    }

    public function testPatchMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request('PATCH', '/diy-form/get-form-tags/100');
    }

    public function testHeadMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request('HEAD', '/diy-form/get-form-tags/100');
    }

    public function testOptionsMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request('OPTIONS', '/diy-form/get-form-tags/100');
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        // @phpstan-ignore-next-line request() 方法的第一个参数在这里必须是变量，因为我们在测试不同的HTTP方法
        $client->request($method, '/diy-form/get-form-tags/100');
    }
}
