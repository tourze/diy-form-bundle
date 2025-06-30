<?php

namespace DiyFormBundle\Tests\Unit\Event;

use DiyFormBundle\Event\SendMobileCaptchaEvent;
use PHPUnit\Framework\TestCase;

class SendMobileCaptchaEventTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(SendMobileCaptchaEvent::class));
    }
}