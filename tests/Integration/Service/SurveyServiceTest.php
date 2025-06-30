<?php

namespace DiyFormBundle\Tests\Integration\Service;

use DiyFormBundle\Service\SurveyService;
use PHPUnit\Framework\TestCase;

class SurveyServiceTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(SurveyService::class));
    }
}