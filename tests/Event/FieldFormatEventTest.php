<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Event;

use DiyFormBundle\Event\FieldFormatEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(FieldFormatEvent::class)]
final class FieldFormatEventTest extends AbstractEventTestCase
{
    public function testResultProperty(): void
    {
        $event = new FieldFormatEvent();
        $this->assertSame([], $event->getResult());

        $result = ['formatted' => true, 'value' => 'test'];
        $event->setResult($result);
        $this->assertSame($result, $event->getResult());
    }
}
