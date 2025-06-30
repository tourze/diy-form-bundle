<?php

namespace DiyFormBundle\Tests\Integration\Controller;

use DiyFormBundle\Controller\SqlController;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Repository\FormRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SqlControllerTest extends TestCase
{
    private SqlController $controller;
    private FormRepository $formRepository;
    private Connection $connection;
    private AbstractPlatform $platform;

    protected function setUp(): void
    {
        $this->controller = new SqlController();
        $this->formRepository = $this->createMock(FormRepository::class);
        $this->connection = $this->createMock(Connection::class);
        $this->platform = $this->createMock(AbstractPlatform::class);
        
        $this->connection->expects($this->any())
            ->method('getDatabasePlatform')
            ->willReturn($this->platform);
    }

    public function test__invoke_表单不存在时抛出异常()
    {
        $this->formRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => '123'])
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('找不到模型数据');

        $this->controller->__invoke('123', $this->formRepository, $this->connection);
    }

    public function test__invoke_生成正确的SQL语句()
    {
        $form = $this->createMock(Form::class);
        $field1 = $this->createMock(Field::class);
        $field2 = $this->createMock(Field::class);
        
        $field1->expects($this->any())->method('getId')->willReturn(1);
        $field1->expects($this->any())->method('getTitle')->willReturn('姓名');
        
        $field2->expects($this->any())->method('getId')->willReturn(2);
        $field2->expects($this->any())->method('getTitle')->willReturn('年龄');
        
        $form->expects($this->any())->method('getId')->willReturn(100);
        $form->expects($this->once())
            ->method('getSortedFields')
            ->willReturn([$field1, $field2]);
        
        $this->formRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => '100'])
            ->willReturn($form);
        
        $this->platform->expects($this->exactly(2))
            ->method('quoteIdentifier')
            ->willReturnCallback(function ($identifier) {
                return "`{$identifier}`";
            });
        
        $response = $this->controller->__invoke('100', $this->formRepository, $this->connection);
        
        $expectedSql = "SELECT ce.id, ce.user_id, ce.start_time, ce.finish_time, v1.input AS `姓名`, v2.input AS `年龄` FROM diy_form_record AS ce\n";
        $expectedSql .= "LEFT JOIN diy_form_data AS v1 ON (ce.id = v1.record_id AND v1.field_id = '1')\n";
        $expectedSql .= "LEFT JOIN diy_form_data AS v2 ON (ce.id = v2.record_id AND v2.field_id = '2')\n";
        $expectedSql .= "WHERE ce.form_id = '100'";
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($expectedSql, $response->getContent());
    }
}