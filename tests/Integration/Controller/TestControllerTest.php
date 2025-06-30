<?php

namespace DiyFormBundle\Tests\Integration\Controller;

use DiyFormBundle\Controller\TestController;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Option;
use DiyFormBundle\Repository\FormRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TestControllerTest extends TestCase
{
    private TestController $controller;
    private FormRepository $formRepository;

    protected function setUp(): void
    {
        $this->controller = new TestController();
        $this->formRepository = $this->createMock(FormRepository::class);
    }

    public function test__invoke_表单不存在时抛出异常()
    {
        $this->formRepository->expects($this->once())
            ->method('find')
            ->with('123')
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('找不到表单配置');

        $this->controller->__invoke('123', $this->formRepository);
    }

    public function test__invoke_验证方法签名()
    {
        $reflectionClass = new \ReflectionClass($this->controller);
        $method = $reflectionClass->getMethod('__invoke');
        
        // 验证方法存在并且是公共的
        $this->assertTrue($method->isPublic());
        $this->assertEquals(2, $method->getNumberOfParameters());
    }
}