<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Enum;

use DiyFormBundle\Enum\SmsReceiveEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\EnumExtra\BadgeInterface;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(SmsReceiveEnum::class)]
final class SmsReceiveEnumTest extends AbstractEnumTestCase
{
    public function testEnum存在(): void
    {
        $this->assertTrue(enum_exists(SmsReceiveEnum::class));
    }

    public function testCases返回所有枚举值(): void
    {
        $cases = SmsReceiveEnum::cases();
        $this->assertCount(2, $cases);
        $this->assertContains(SmsReceiveEnum::SENT, $cases);
        $this->assertContains(SmsReceiveEnum::REJECT, $cases);
    }

    public function testToArray(): void
    {
        $sent = SmsReceiveEnum::SENT;
        $sentResult = $sent->toArray();
        $this->assertIsArray($sentResult);
        $this->assertArrayHasKey('value', $sentResult);
        $this->assertArrayHasKey('label', $sentResult);
        $this->assertEquals(1, $sentResult['value']);
        $this->assertEquals('已发送', $sentResult['label']);

        $reject = SmsReceiveEnum::REJECT;
        $rejectResult = $reject->toArray();
        $this->assertIsArray($rejectResult);
        $this->assertArrayHasKey('value', $rejectResult);
        $this->assertArrayHasKey('label', $rejectResult);
        $this->assertEquals(0, $rejectResult['value']);
        $this->assertEquals('已退回', $rejectResult['label']);
    }

    public function testGetBadge(): void
    {
        $this->assertEquals(BadgeInterface::SUCCESS, SmsReceiveEnum::SENT->getBadge());
        $this->assertEquals(BadgeInterface::DANGER, SmsReceiveEnum::REJECT->getBadge());
    }

    public function testImplementsBadgeInterface(): void
    {
        $this->assertInstanceOf(BadgeInterface::class, SmsReceiveEnum::SENT);
        $this->assertInstanceOf(BadgeInterface::class, SmsReceiveEnum::REJECT);
    }
}
