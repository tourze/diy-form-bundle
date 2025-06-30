<?php

namespace DiyFormBundle\Tests\Unit\Event;

use DiyFormBundle\Event\OptionsFormatEvent;
use PHPUnit\Framework\TestCase;

class OptionsFormatEventTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(OptionsFormatEvent::class));
    }
}