<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Event;

use DiyFormBundle\Event\RecordAfterAnalyseEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(RecordAfterAnalyseEvent::class)]
final class RecordAfterAnalyseEventTest extends AbstractEventTestCase
{
    public function testResultProperty(): void
    {
        $event = new RecordAfterAnalyseEvent();
        $this->assertSame([], $event->getResult());

        $result = ['analysed' => true, 'tags' => ['tag1', 'tag2']];
        $event->setResult($result);
        $this->assertSame($result, $event->getResult());
    }
}
