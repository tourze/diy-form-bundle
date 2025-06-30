<?php

namespace DiyFormBundle\Tests\Unit\Event;

use DiyFormBundle\Event\FieldFormatEvent;
use PHPUnit\Framework\TestCase;

class FieldFormatEventTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(FieldFormatEvent::class));
    }
}