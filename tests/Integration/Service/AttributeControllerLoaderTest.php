<?php

namespace DiyFormBundle\Tests\Integration\Service;

use DiyFormBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\TestCase;

class AttributeControllerLoaderTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(AttributeControllerLoader::class));
    }
}