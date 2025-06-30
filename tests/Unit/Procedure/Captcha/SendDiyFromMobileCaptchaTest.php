<?php

namespace DiyFormBundle\Tests\Unit\Procedure\Captcha;

use DiyFormBundle\Procedure\Captcha\SendDiyFromMobileCaptcha;
use PHPUnit\Framework\TestCase;

class SendDiyFromMobileCaptchaTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(SendDiyFromMobileCaptcha::class));
    }
}