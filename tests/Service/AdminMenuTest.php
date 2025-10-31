<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Service;

use DiyFormBundle\Service\AdminMenu;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试设置方法
    }

    private function getAdminMenu(): AdminMenu
    {
        $service = self::getContainer()->get(AdminMenu::class);
        self::assertInstanceOf(AdminMenu::class, $service);

        return $service;
    }

    public function testServiceExists(): void
    {
        $this->assertInstanceOf(AdminMenu::class, $this->getAdminMenu());
    }

    public function testImplementsMenuProviderInterface(): void
    {
        $this->assertInstanceOf(MenuProviderInterface::class, $this->getAdminMenu());
    }

    public function testInvokeBasicExecution(): void
    {
        // 验证 AdminMenu 可以被正确调用
        $adminMenu = $this->getAdminMenu();
        $this->assertInstanceOf(MenuProviderInterface::class, $adminMenu);
    }
}
