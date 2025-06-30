<?php

namespace DiyFormBundle\Tests\Unit\Procedure\Record;

use DiyFormBundle\Procedure\Record\GetMyInviteFormRecordList;
use PHPUnit\Framework\TestCase;

class GetMyInviteFormRecordListTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(GetMyInviteFormRecordList::class));
    }
}