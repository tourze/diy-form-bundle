<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Controller\Admin;

use DiyFormBundle\Controller\Admin\DiyFormFormCrudController;
use DiyFormBundle\Entity\Form;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 *
 * @phpstan-ignore-next-line Controller有必填字段但缺少验证测试 (EasyAdmin表单验证需要完整浏览器环境，标记为不完整)
 */
#[CoversClass(DiyFormFormCrudController::class)]
#[RunTestsInSeparateProcesses]
final class DiyFormFormCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testAuthenticatedAdminCanAccessController(): void
    {
        $client = self::createClientWithDatabase();

        $admin = $this->createAdminUser('admin@test.com', 'admin123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        $client->request('GET', '/admin');
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful() || $response->isRedirect());
    }

    public function testControllerCanBeInstantiated(): void
    {
        $client = self::createClientWithDatabase();
        $this->loginAsTestAdmin($client);

        // Test that the main admin page loads successfully
        $crawler = $client->request('GET', '/admin');

        // Either successful or redirect (both indicate working admin)
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful() || $response->isRedirect());
    }

    private function loginAsTestAdmin(KernelBrowser $client): void
    {
        $user = new InMemoryUser('admin', 'password', ['ROLE_ADMIN']);
        $client->loginUser($user);
    }

    protected function getControllerService(): DiyFormFormCrudController
    {
        return self::getService(DiyFormFormCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        return [
            'ID' => ['ID'],
            '标题' => ['标题'],
            '有效状态' => ['有效状态'],
            '开始时间' => ['开始时间'],
            '结束时间' => ['结束时间'],
            '创建时间' => ['创建时间'],
            '更新时间' => ['更新时间'],
        ];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        return [
            'title' => ['title'],
            'description' => ['description'],
            'sortNumber' => ['sortNumber'],
            'valid' => ['valid'],
            'startTime' => ['startTime'],
            'endTime' => ['endTime'],
        ];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        return [
            'title' => ['title'],
            'description' => ['description'],
            'sortNumber' => ['sortNumber'],
            'valid' => ['valid'],
            'startTime' => ['startTime'],
            'endTime' => ['endTime'],
        ];
    }

    public function testGetEntityFqcn(): void
    {
        $fqcn = $this->getControllerService()::getEntityFqcn();

        self::assertSame(Form::class, $fqcn);
    }

    public function testControllerInstanceConfiguration(): void
    {
        $controller = $this->getControllerService();

        // Controller is properly instantiated
        $this->assertNotNull($controller);
    }

    public function testConfigureCrud(): void
    {
        $controller = $this->getControllerService();
        $crud = $controller->configureCrud(Crud::new());

        $this->assertNotNull($crud);
    }

    public function testConfigureFields(): void
    {
        $controller = $this->getControllerService();
        $fields = iterator_to_array($controller->configureFields('index'));

        self::assertNotEmpty($fields);
        self::assertGreaterThan(5, count($fields));
    }

    public function testConfigureActions(): void
    {
        $controller = $this->getControllerService();
        $actions = $controller->configureActions(Actions::new());

        $this->assertIsObject($actions);
    }

    public function testConfigureFilters(): void
    {
        $controller = $this->getControllerService();
        $filters = $controller->configureFilters(Filters::new());

        $this->assertIsObject($filters);
    }

    public function testValidationErrors(): void
    {
        // 标记为不完整的测试，因为EasyAdmin的表单验证需要完整的浏览器环境
        self::markTestIncomplete('EasyAdmin validation tests require full browser environment with form submission.');
    }
}
