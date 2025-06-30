<?php

namespace DiyFormBundle\Tests\Unit\Event;

use DiyFormBundle\Event\BeforeAnswerSingleDiyFormEvent;
use PHPUnit\Framework\TestCase;

class BeforeAnswerSingleDiyFormEventTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(BeforeAnswerSingleDiyFormEvent::class));
    }
}