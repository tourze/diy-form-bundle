<?php

namespace DiyFormBundle\Tests\Unit\Event;

use DiyFormBundle\Event\RecordAfterAnalyseEvent;
use PHPUnit\Framework\TestCase;

class RecordAfterAnalyseEventTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(RecordAfterAnalyseEvent::class));
    }
}