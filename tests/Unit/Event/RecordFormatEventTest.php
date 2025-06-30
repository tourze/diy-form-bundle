<?php

namespace DiyFormBundle\Tests\Unit\Event;

use DiyFormBundle\Event\RecordFormatEvent;
use PHPUnit\Framework\TestCase;

class RecordFormatEventTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(RecordFormatEvent::class));
    }
}