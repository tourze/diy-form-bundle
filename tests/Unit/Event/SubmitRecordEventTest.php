<?php

namespace DiyFormBundle\Tests\Unit\Event;

use DiyFormBundle\Entity\Record;
use DiyFormBundle\Event\SubmitRecordEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class SubmitRecordEventTest extends TestCase
{
    private SubmitRecordEvent $event;

    protected function setUp(): void
    {
        $this->event = new SubmitRecordEvent();
    }

    public function testSetAndGetUser_正确设置和获取用户()
    {
        $user = $this->createMock(UserInterface::class);
        $this->event->setUser($user);
        $this->assertSame($user, $this->event->getUser());
    }

    public function testSetAndGetRecord_正确设置和获取记录()
    {
        $record = new Record();
        $this->event->setRecord($record);
        $this->assertSame($record, $this->event->getRecord());
    }
}