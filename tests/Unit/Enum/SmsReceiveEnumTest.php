<?php

namespace DiyFormBundle\Tests\Unit\Enum;

use DiyFormBundle\Enum\SmsReceiveEnum;
use PHPUnit\Framework\TestCase;

class SmsReceiveEnumTest extends TestCase
{
    public function testEnum_存在()
    {
        $this->assertTrue(enum_exists(SmsReceiveEnum::class));
    }

    public function testCases_返回所有枚举值()
    {
        $cases = SmsReceiveEnum::cases();
        $this->assertCount(2, $cases);
        $this->assertContains(SmsReceiveEnum::SENT, $cases);
        $this->assertContains(SmsReceiveEnum::REJECT, $cases);
    }
}