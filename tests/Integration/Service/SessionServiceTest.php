<?php

namespace DiyFormBundle\Tests\Integration\Service;

use DiyFormBundle\Service\SessionService;
use PHPUnit\Framework\TestCase;

class SessionServiceTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(SessionService::class));
    }
}