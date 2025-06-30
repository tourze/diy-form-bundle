<?php

namespace DiyFormBundle\Tests\Unit\Procedure\Step;

use DiyFormBundle\Procedure\Step\SubmitDiyFormRecord;
use PHPUnit\Framework\TestCase;

class SubmitDiyFormRecordTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(SubmitDiyFormRecord::class));
    }
}