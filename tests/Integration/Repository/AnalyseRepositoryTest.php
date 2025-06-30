<?php

namespace DiyFormBundle\Tests\Integration\Repository;

use DiyFormBundle\Repository\AnalyseRepository;
use PHPUnit\Framework\TestCase;

class AnalyseRepositoryTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(AnalyseRepository::class));
    }
}