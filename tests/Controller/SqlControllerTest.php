<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Controller;

use DiyFormBundle\Controller\SqlController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(SqlController::class)]
#[RunTestsInSeparateProcesses]
final class SqlControllerTest extends AbstractWebTestCase
{
    public function testUnauthenticatedAccessReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request('GET', '/diy-form-sql/123');
    }

    public function testGetMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request('GET', '/diy-form-sql/100');
    }

    public function testPostMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request('POST', '/diy-form-sql/100');
    }

    public function testPutMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request('PUT', '/diy-form-sql/100');
    }

    public function testDeleteMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request('DELETE', '/diy-form-sql/100');
    }

    public function testPatchMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request('PATCH', '/diy-form-sql/100');
    }

    public function testHeadMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request('HEAD', '/diy-form-sql/100');
    }

    public function testOptionsMethodReturnsNotFound(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request('OPTIONS', '/diy-form-sql/100');
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);

        $client->request($method, '/diy-form-sql/100');
    }
}
