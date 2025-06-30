<?php

namespace DiyFormBundle\Tests\Integration\Repository;

use DiyFormBundle\Repository\OptionRepository;
use PHPUnit\Framework\TestCase;

class OptionRepositoryTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(OptionRepository::class));
    }
}