<?php

namespace DiyFormBundle\Tests\Unit\Procedure\Step;

use DiyFormBundle\Procedure\Step\CreateDiyFormRecord;
use PHPUnit\Framework\TestCase;

class CreateDiyFormRecordTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(CreateDiyFormRecord::class));
    }
}