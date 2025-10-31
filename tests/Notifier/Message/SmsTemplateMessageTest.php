<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Notifier\Message;

use DiyFormBundle\Notifier\Message\SmsTemplateMessage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SmsTemplateMessage::class)]
final class SmsTemplateMessageTest extends TestCase
{
    public function testMessageCanBeInstantiated(): void
    {
        $message = new SmsTemplateMessage('13800138000', 'Test message');
        $this->assertInstanceOf(SmsTemplateMessage::class, $message);
    }

    public function testTemplateCodeProperty(): void
    {
        $message = new SmsTemplateMessage('13800138000', 'Test message');
        $message->setTemplateCode('SMS_123456');
        $this->assertSame('SMS_123456', $message->getTemplateCode());
    }

    public function testSignNameProperty(): void
    {
        $message = new SmsTemplateMessage('13800138000', 'Test message');
        $message->setSignName('测试签名');
        $this->assertSame('测试签名', $message->getSignName());
    }

    public function testTemplateParamProperty(): void
    {
        $message = new SmsTemplateMessage('13800138000', 'Test message');
        $params = ['code' => '123456', 'name' => '张三'];
        $message->setTemplateParam($params);
        $this->assertSame($params, $message->getTemplateParam());
    }
}
