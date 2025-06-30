<?php

namespace DiyFormBundle\Tests\Unit\Procedure\Record;

use DiyFormBundle\Procedure\Record\DeleteDiyFormRecord;
use PHPUnit\Framework\TestCase;

class DeleteDiyFormRecordTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(DeleteDiyFormRecord::class));
    }
}