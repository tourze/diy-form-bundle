<?php

namespace DiyFormBundle\Tests\Unit\Event;

use DiyFormBundle\Event\GetRecordDetailEvent;
use PHPUnit\Framework\TestCase;

class GetRecordDetailEventTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(GetRecordDetailEvent::class));
    }
}