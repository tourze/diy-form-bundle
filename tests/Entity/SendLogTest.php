<?php

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\SendLog;
use DiyFormBundle\Enum\SmsReceiveEnum;
use PHPUnit\Framework\TestCase;

class SendLogTest extends TestCase
{
    private SendLog $sendLog;

    protected function setUp(): void
    {
        $this->sendLog = new SendLog();
    }

    public function testId_初始值为0()
    {
        $this->assertEquals(0, $this->sendLog->getId());
    }

    public function testBatchId_可以设置和获取()
    {
        $this->sendLog->setBatchId('batch123');
        $this->assertEquals('batch123', $this->sendLog->getBatchId());
    }

    public function testMobile_可以设置和获取()
    {
        $this->sendLog->setMobile('13800138000');
        $this->assertEquals('13800138000', $this->sendLog->getMobile());
    }

    public function testZone_可以设置和获取()
    {
        $this->sendLog->setZone('+86');
        $this->assertEquals('+86', $this->sendLog->getZone());
    }

    public function testMemo_可以设置和获取()
    {
        $this->sendLog->setMemo('退回原因');
        $this->assertEquals('退回原因', $this->sendLog->getMemo());
    }

    public function testStatus_可以设置和获取()
    {
        $this->sendLog->setStatus(SmsReceiveEnum::SENT);
        $this->assertEquals(SmsReceiveEnum::SENT, $this->sendLog->getStatus());
    }

    public function test__toString_返回日志描述()
    {
        // 设置一个非0的ID以确保__toString不返回空字符串
        $reflection = new \ReflectionClass($this->sendLog);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->sendLog, 1);
        
        $this->sendLog->setMobile('13800138000');
        $this->sendLog->setStatus(SmsReceiveEnum::SENT);
        $result = (string) $this->sendLog;
        $this->assertStringContainsString('13800138000', $result);
        $this->assertStringContainsString('1', $result);
    }

    public function test__toString_ID为0时返回空字符串()
    {
        $this->sendLog->setMobile('13800138000');
        $this->sendLog->setStatus(SmsReceiveEnum::SENT);
        $result = (string) $this->sendLog;
        $this->assertEquals('', $result);
    }
}