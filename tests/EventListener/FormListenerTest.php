<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\EventListener;

use DiyFormBundle\Entity\Form;
use DiyFormBundle\EventListener\FormListener;
use DiyFormBundle\Repository\RecordRepository;
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

    public function testPreRemove(): void
    {
        // 从容器获取监听器和依赖
        $recordRepository = $this->createMock(RecordRepository::class);
        $form = $this->createMock(Form::class);

        $recordRepository->expects($this->once())
            ->method('count')
            ->with(['form' => $form])
            ->willReturn(0)
        ;

        // 将 mock 服务设置到容器中
        self::getContainer()->set(RecordRepository::class, $recordRepository);
        $listener = self::getService(FormListener::class);

        // 验证方法调用不抛出异常
        $listener->preRemove($form);
    }

    public function testPreRemoveWithExistingRecords(): void
    {
        $recordRepository = $this->createMock(RecordRepository::class);
        $form = $this->createMock(Form::class);

        $recordRepository->expects($this->once())
            ->method('count')
            ->with(['form' => $form])
            ->willReturn(5)
        ;

        // 将 mock 服务设置到容器中
        self::getContainer()->set(RecordRepository::class, $recordRepository);
        $listener = self::getService(FormListener::class);

        $this->expectException(\Exception::class);
        $listener->preRemove($form);
    }
}
