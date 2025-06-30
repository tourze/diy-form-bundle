<?php

namespace DiyFormBundle\Tests\Unit\Event;

use DiyFormBundle\Event\RecordAnalyseTriggerEvent;
use PHPUnit\Framework\TestCase;

class RecordAnalyseTriggerEventTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(RecordAnalyseTriggerEvent::class));
    }
}