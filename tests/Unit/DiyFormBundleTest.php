<?php

namespace DiyFormBundle\Tests\Unit;

use DiyFormBundle\DiyFormBundle;
use PHPUnit\Framework\TestCase;

class DiyFormBundleTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(DiyFormBundle::class));
    }
}