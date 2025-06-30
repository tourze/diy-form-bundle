<?php

namespace DiyFormBundle\Tests\Unit\DependencyInjection;

use DiyFormBundle\DependencyInjection\DiyFormExtension;
use PHPUnit\Framework\TestCase;

class DiyFormExtensionTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(DiyFormExtension::class));
    }
}