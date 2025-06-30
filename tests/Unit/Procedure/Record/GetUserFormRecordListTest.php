<?php

namespace DiyFormBundle\Tests\Unit\Procedure\Record;

use DiyFormBundle\Procedure\Record\GetUserFormRecordList;
use PHPUnit\Framework\TestCase;

class GetUserFormRecordListTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(GetUserFormRecordList::class));
    }
}