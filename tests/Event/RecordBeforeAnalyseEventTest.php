<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Event;

use DiyFormBundle\Event\RecordBeforeAnalyseEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(RecordBeforeAnalyseEvent::class)]
final class RecordBeforeAnalyseEventTest extends AbstractEventTestCase
{
    public function testResultProperty(): void
    {
        $event = new RecordBeforeAnalyseEvent();
        $this->assertSame([], $event->getResult());

        $result = ['key' => 'value', 'data' => [1, 2, 3]];
        $event->setResult($result);
        $this->assertSame($result, $event->getResult());
    }
}
