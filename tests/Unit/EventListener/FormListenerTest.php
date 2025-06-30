<?php

namespace DiyFormBundle\Tests\Unit\EventListener;

use DiyFormBundle\EventListener\FormListener;
use PHPUnit\Framework\TestCase;

class FormListenerTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(FormListener::class));
    }
}