<?php

namespace DiyFormBundle\Tests\Integration\Repository;

use DiyFormBundle\Repository\RecordRepository;
use PHPUnit\Framework\TestCase;

class RecordRepositoryTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(RecordRepository::class));
    }
}