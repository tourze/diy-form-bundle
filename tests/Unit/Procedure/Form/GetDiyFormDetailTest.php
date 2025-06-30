<?php

namespace DiyFormBundle\Tests\Unit\Procedure\Form;

use DiyFormBundle\Procedure\Form\GetDiyFormDetail;
use PHPUnit\Framework\TestCase;

class GetDiyFormDetailTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(GetDiyFormDetail::class));
    }
}