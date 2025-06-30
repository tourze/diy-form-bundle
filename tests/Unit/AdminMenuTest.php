<?php

namespace DiyFormBundle\Tests\Unit;

use DiyFormBundle\AdminMenu;
use DiyFormBundle\Entity\Form;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

class AdminMenuTest extends TestCase
{
    private AdminMenu $adminMenu;
    private LinkGeneratorInterface $linkGenerator;

    protected function setUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $this->adminMenu = new AdminMenu($this->linkGenerator);
    }

    public function test__invoke_添加装修中心菜单()
    {
        $item = $this->createMock(ItemInterface::class);
        $decorationCenter = $this->createMock(ItemInterface::class);
        
        $this->linkGenerator->expects($this->once())
            ->method('getCurdListPage')
            ->with(Form::class)
            ->willReturn('/admin/diy-form');
        
        $item->expects($this->exactly(2))
            ->method('getChild')
            ->with('装修中心')
            ->willReturnOnConsecutiveCalls(null, $decorationCenter);
        
        $item->expects($this->once())
            ->method('addChild')
            ->with('装修中心')
            ->willReturn($decorationCenter);
        
        $decorationCenter->expects($this->once())
            ->method('addChild')
            ->with('表单装修')
            ->willReturnSelf();
        
        $decorationCenter->expects($this->once())
            ->method('setUri')
            ->with('/admin/diy-form');
        
        $this->adminMenu->__invoke($item);
    }

    public function test__invoke_已有装修中心菜单时直接添加子菜单()
    {
        $item = $this->createMock(ItemInterface::class);
        $decorationCenter = $this->createMock(ItemInterface::class);
        
        $this->linkGenerator->expects($this->once())
            ->method('getCurdListPage')
            ->with(Form::class)
            ->willReturn('/admin/diy-form');
        
        $item->expects($this->exactly(2))
            ->method('getChild')
            ->with('装修中心')
            ->willReturn($decorationCenter);
        
        $item->expects($this->never())
            ->method('addChild');
        
        $decorationCenter->expects($this->once())
            ->method('addChild')
            ->with('表单装修')
            ->willReturnSelf();
        
        $decorationCenter->expects($this->once())
            ->method('setUri')
            ->with('/admin/diy-form');
        
        $this->adminMenu->__invoke($item);
    }
}