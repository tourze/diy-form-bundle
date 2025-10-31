<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Event;

use DiyFormBundle\Entity\Record;
use DiyFormBundle\Event\SubmitRecordEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(SubmitRecordEvent::class)]
final class SubmitRecordEventTest extends AbstractEventTestCase
{
    private SubmitRecordEvent $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->event = new SubmitRecordEvent();
    }

    public function testSetAndGetUser正确设置和获取用户(): void
    {
        $user = $this->createMock(UserInterface::class);
        $this->event->setUser($user);
        $this->assertSame($user, $this->event->getUser());
    }

    public function testSetAndGetRecord正确设置和获取记录(): void
    {
        $record = new Record();
        $this->event->setRecord($record);
        $this->assertSame($record, $this->event->getRecord());
    }
}
