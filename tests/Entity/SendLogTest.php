<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\SendLog;
use DiyFormBundle\Enum\SmsReceiveEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(SendLog::class)]
final class SendLogTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new SendLog();
    }

    /**
     * @return iterable<array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'batchId' => ['batchId', 'batch123'];
        yield 'mobile' => ['mobile', '13800138000'];
        yield 'zone' => ['zone', '+86'];
        yield 'memo' => ['memo', '退回原因'];
        yield 'status' => ['status', SmsReceiveEnum::SENT];
    }

    public function testToString返回日志描述(): void
    {
        $sendLog = new SendLog();
        // 设置一个非0的ID以确保__toString不返回空字符串
        $reflection = new \ReflectionClass($sendLog);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($sendLog, 1);

        $sendLog->setMobile('13800138000');
        $sendLog->setStatus(SmsReceiveEnum::SENT);
        $result = (string) $sendLog;
        $this->assertStringContainsString('13800138000', $result);
        $this->assertStringContainsString('1', $result);
    }

    public function testToStringID为0时返回空字符串(): void
    {
        $sendLog = new SendLog();
        $sendLog->setMobile('13800138000');
        $sendLog->setStatus(SmsReceiveEnum::SENT);
        $result = (string) $sendLog;
        $this->assertEquals('', $result);
    }
}
