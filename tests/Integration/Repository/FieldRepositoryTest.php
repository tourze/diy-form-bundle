<?php

namespace DiyFormBundle\Tests\Integration\Repository;

use DiyFormBundle\Repository\FieldRepository;
use PHPUnit\Framework\TestCase;

class FieldRepositoryTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(FieldRepository::class));
    }
}