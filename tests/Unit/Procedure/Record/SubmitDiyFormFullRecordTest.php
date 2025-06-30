<?php

namespace DiyFormBundle\Tests\Unit\Procedure\Record;

use DiyFormBundle\Procedure\Record\SubmitDiyFormFullRecord;
use PHPUnit\Framework\TestCase;

class SubmitDiyFormFullRecordTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(SubmitDiyFormFullRecord::class));
    }
}