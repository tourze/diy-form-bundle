<?php

namespace DiyFormBundle\Tests\Integration\Repository;

use DiyFormBundle\Repository\DataRepository;
use PHPUnit\Framework\TestCase;

class DataRepositoryTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(DataRepository::class));
    }
}