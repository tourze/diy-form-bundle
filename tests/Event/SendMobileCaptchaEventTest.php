<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Event;

use DiyFormBundle\Event\SendMobileCaptchaEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(SendMobileCaptchaEvent::class)]
final class SendMobileCaptchaEventTest extends AbstractEventTestCase
{
    public function testPhoneNumberProperty(): void
    {
        $event = new SendMobileCaptchaEvent();
        $phoneNumber = '13800138000';

        $event->setPhoneNumber($phoneNumber);
        $this->assertSame($phoneNumber, $event->getPhoneNumber());
    }

    public function testCodeProperty(): void
    {
        $event = new SendMobileCaptchaEvent();
        $code = '123456';

        $event->setCode($code);
        $this->assertSame($code, $event->getCode());
    }

    public function testSentProperty(): void
    {
        $event = new SendMobileCaptchaEvent();
        $this->assertFalse($event->isSent());

        $event->setSent(true);
        $this->assertTrue($event->isSent());

        $event->setSent(false);
        $this->assertFalse($event->isSent());
    }
}
