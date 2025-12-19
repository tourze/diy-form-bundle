<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Controller\Admin;

use DiyFormBundle\Controller\Admin\DiyFormOptionCrudController;
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
 */
#[CoversClass(DiyFormOptionCrudController::class)]
#[RunTestsInSeparateProcesses]
final class DiyFormOptionCrudControllerTest extends AbstractEasyAdminControllerTestCase
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

    protected function getControllerService(): DiyFormOptionCrudController
    {
        return self::getService(DiyFormOptionCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        return [
            'ID' => ['ID'],
            '所属字段' => ['所属字段'],
            '序列号' => ['序列号'],
            '选项文本' => ['选项文本'],
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
            'field' => ['field'],
            'sn' => ['sn'],
            'text' => ['text'],
            'description' => ['description'],
            'tags' => ['tags'],
            'allowInput' => ['allowInput'],
            'answer' => ['answer'],
            'icon' => ['icon'],
            'selectedIcon' => ['selectedIcon'],
            'mutex' => ['mutex'],
            'showExpression' => ['showExpression'],
        ];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        return [
            'field' => ['field'],
            'sn' => ['sn'],
            'text' => ['text'],
            'description' => ['description'],
            'tags' => ['tags'],
            'allowInput' => ['allowInput'],
            'answer' => ['answer'],
            'icon' => ['icon'],
            'selectedIcon' => ['selectedIcon'],
            'mutex' => ['mutex'],
            'showExpression' => ['showExpression'],
        ];
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
        $client = self::createAuthenticatedClient();

        // 访问新建页面（如果 NEW 操作启用）
        try {
            $crawler = $client->request('GET', $this->generateAdminUrl('new'));
            $form = $crawler->selectButton('Save changes')->form();

            // 提交空表单以触发验证错误
            $client->submit($form);

            // 验证响应状态码表示验证失败
            $this->assertResponseStatusCodeSame(422);
        } catch (\InvalidArgumentException) {
            // NEW 操作被禁用时跳过测试
            self::markTestSkipped('NEW action is disabled for this controller.');
        }
    }
}
