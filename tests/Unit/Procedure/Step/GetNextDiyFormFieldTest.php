<?php

namespace DiyFormBundle\Tests\Unit\Procedure\Step;

use DiyFormBundle\Procedure\Step\GetNextDiyFormField;
use PHPUnit\Framework\TestCase;

class GetNextDiyFormFieldTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(GetNextDiyFormField::class));
    }
}