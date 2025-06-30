<?php

namespace DiyFormBundle\Tests\Integration\Repository;

use DiyFormBundle\Repository\SmsDsnRepository;
use PHPUnit\Framework\TestCase;

class SmsDsnRepositoryTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(SmsDsnRepository::class));
    }
}