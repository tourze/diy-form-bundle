<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Event;

use DiyFormBundle\Event\SubmitDiyFormFullRecordEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(SubmitDiyFormFullRecordEvent::class)]
final class SubmitDiyFormFullRecordEventTest extends AbstractEventTestCase
{
    public function testJsonRpcResultProperty(): void
    {
        $event = new SubmitDiyFormFullRecordEvent();
        $this->assertSame([], $event->getJsonRpcResult());

        $result = ['success' => true, 'recordId' => 123];
        $event->setJsonRpcResult($result);
        $this->assertSame($result, $event->getJsonRpcResult());
    }
}
