<?php

namespace DiyFormBundle\Tests\Unit\Event;

use DiyFormBundle\Event\AnswerTagCalcEvent;
use PHPUnit\Framework\TestCase;

class AnswerTagCalcEventTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(AnswerTagCalcEvent::class));
    }
}