<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\EventListener;

use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\EventListener\FormListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(FormListener::class)]
#[RunTestsInSeparateProcesses]
final class FormListenerTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试初始化
    }

    private function createForm(string $title): Form
    {
        $form = new Form();
        $form->setTitle($title);
        $form->setValid(true);
        $form->setStartTime(new \DateTimeImmutable());
        $form->setEndTime(new \DateTimeImmutable('+1 hour'));

        $em = self::getEntityManager();
        $em->persist($form);
        $em->flush();

        return $form;
    }

    private function createRecord(Form $form): Record
    {
        $record = new Record();
        $record->setForm($form);
        $record->setStartTime(new \DateTimeImmutable());
        $record->setFinished(false);

        $em = self::getEntityManager();
        $em->persist($record);
        $em->flush();

        return $record;
    }

    public function testPreRemove(): void
    {
        // 创建真实的Form实体（没有关联的Record）
        $form = $this->createForm('测试表单无记录-' . uniqid());

        // 从容器获取监听器
        $listener = self::getService(FormListener::class);

        // 验证方法调用不抛出异常（因为没有关联的Record）
        $listener->preRemove($form);
        $this->assertTrue(true); // 如果到达这里，说明没有抛出异常
    }

    public function testPreRemoveWithExistingRecords(): void
    {
        // 创建真实的Form实体
        $form = $this->createForm('测试表单有记录-' . uniqid());

        // 创建关联的Record
        $this->createRecord($form);

        // 从容器获取监听器
        $listener = self::getService(FormListener::class);

        // 验证方法调用抛出异常（因为有关联的Record）
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('该调查问卷已被使用，无法删除');
        $listener->preRemove($form);
    }
}
