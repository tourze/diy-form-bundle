<?php

namespace DiyFormBundle\Tests\Unit\Session;

use DiyFormBundle\Session\NextField;
use PHPUnit\Framework\TestCase;

class NextFieldTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(NextField::class));
    }
}