<?php

namespace DiyFormBundle\Tests\Unit\Event;

use DiyFormBundle\Event\SubmitDiyFormFullRecordEvent;
use PHPUnit\Framework\TestCase;

class SubmitDiyFormFullRecordEventTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(SubmitDiyFormFullRecordEvent::class));
    }
}