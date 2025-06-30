<?php

namespace DiyFormBundle\Tests\Unit\Event;

use DiyFormBundle\Event\RecordBeforeAnalyseEvent;
use PHPUnit\Framework\TestCase;

class RecordBeforeAnalyseEventTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(RecordBeforeAnalyseEvent::class));
    }
}