<?php

namespace DiyFormBundle\Tests\Unit\Procedure\Record;

use DiyFormBundle\Procedure\Record\GetDiyFormRecordDetail;
use PHPUnit\Framework\TestCase;

class GetDiyFormRecordDetailTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(GetDiyFormRecordDetail::class));
    }
}