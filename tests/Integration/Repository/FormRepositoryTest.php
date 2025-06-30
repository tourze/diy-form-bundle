<?php

namespace DiyFormBundle\Tests\Integration\Repository;

use DiyFormBundle\Repository\FormRepository;
use PHPUnit\Framework\TestCase;

class FormRepositoryTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(FormRepository::class));
    }
}