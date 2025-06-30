<?php

namespace DiyFormBundle\Tests\Unit\Notifier\Message;

use DiyFormBundle\Notifier\Message\SmsTemplateMessage;
use PHPUnit\Framework\TestCase;

class SmsTemplateMessageTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(SmsTemplateMessage::class));
    }
}