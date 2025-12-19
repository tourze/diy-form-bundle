<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Service;

use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Service\ExpressionEngineService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ExpressionEngineService::class)]
#[RunTestsInSeparateProcesses]
final class ExpressionEngineServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试基类要求的初始化方法
    }

    private function createFormAndRecord(): Record
    {
        $form = new Form();
        $form->setTitle('测试表单-' . uniqid());
        $form->setValid(true);
        $form->setStartTime(new \DateTimeImmutable());
        $form->setEndTime(new \DateTimeImmutable('+1 hour'));

        $record = new Record();
        $record->setForm($form);
        $record->setStartTime(new \DateTimeImmutable());
        $record->setFinished(false);

        $em = self::getEntityManager();
        $em->persist($form);
        $em->persist($record);
        $em->flush();

        return $record;
    }

    public function testServiceCanBeInstantiated(): void
    {
        $service = self::getService(ExpressionEngineService::class);
        $this->assertInstanceOf(ExpressionEngineService::class, $service);
    }

    public function testEvaluateWithRecord(): void
    {
        $service = self::getService(ExpressionEngineService::class);

        // 创建真实的Record对象用于测试
        $record = $this->createFormAndRecord();

        // 测试简单表达式
        $result = $service->evaluateWithRecord('1 + 1', $record, []);
        $this->assertEquals(2, $result);

        // 测试带有values参数的表达式
        $result = $service->evaluateWithRecord('x + y', $record, [], ['x' => 3, 'y' => 4]);
        $this->assertEquals(7, $result);

        // 测试字符串表达式
        $result = $service->evaluateWithRecord('"hello"', $record, []);
        $this->assertEquals('hello', $result);
    }

    public function testEvaluateWithRecordAccessesFormAndRecordVariables(): void
    {
        $service = self::getService(ExpressionEngineService::class);

        // 创建真实的Record对象用于测试
        $record = $this->createFormAndRecord();

        // 测试访问 record 变量
        $result = $service->evaluateWithRecord('record', $record, []);
        $this->assertSame($record, $result);

        // 测试访问 form 变量
        $result = $service->evaluateWithRecord('form', $record, []);
        $this->assertSame($record->getForm(), $result);
    }
}
