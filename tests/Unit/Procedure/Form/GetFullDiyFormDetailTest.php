<?php

namespace DiyFormBundle\Tests\Unit\Procedure\Form;

use DiyFormBundle\Procedure\Form\GetFullDiyFormDetail;
use PHPUnit\Framework\TestCase;

class GetFullDiyFormDetailTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(GetFullDiyFormDetail::class));
    }
}