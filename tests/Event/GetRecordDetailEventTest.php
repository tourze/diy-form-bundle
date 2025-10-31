<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Event;

use DiyFormBundle\Event\GetRecordDetailEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(GetRecordDetailEvent::class)]
final class GetRecordDetailEventTest extends AbstractEventTestCase
{
    public function testResultProperty(): void
    {
        $event = new GetRecordDetailEvent();
        $this->assertSame([], $event->getResult());

        $result = ['id' => 1, 'data' => ['field1' => 'value1']];
        $event->setResult($result);
        $this->assertSame($result, $event->getResult());
    }
}
