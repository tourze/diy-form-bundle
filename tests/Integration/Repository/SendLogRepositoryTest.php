<?php

namespace DiyFormBundle\Tests\Integration\Repository;

use DiyFormBundle\Repository\SendLogRepository;
use PHPUnit\Framework\TestCase;

class SendLogRepositoryTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(SendLogRepository::class));
    }
}