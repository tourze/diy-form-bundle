<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Event;

use DiyFormBundle\Event\RecordFormatEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(RecordFormatEvent::class)]
final class RecordFormatEventTest extends AbstractEventTestCase
{
    public function testResultProperty(): void
    {
        $event = new RecordFormatEvent();
        $this->assertSame([], $event->getResult());

        $result = ['formatted' => true, 'record' => ['id' => 1]];
        $event->setResult($result);
        $this->assertSame($result, $event->getResult());
    }
}
