<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Event;

use DiyFormBundle\Event\RecordAnalyseTriggerEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(RecordAnalyseTriggerEvent::class)]
final class RecordAnalyseTriggerEventTest extends AbstractEventTestCase
{
    public function testResultProperty(): void
    {
        $event = new RecordAnalyseTriggerEvent();
        $this->assertSame([], $event->getResult());

        $result = ['triggered' => true, 'status' => 'processing'];
        $event->setResult($result);
        $this->assertSame($result, $event->getResult());
    }
}
