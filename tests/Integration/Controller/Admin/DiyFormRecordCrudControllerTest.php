<?php

namespace DiyFormBundle\Tests\Integration\Controller\Admin;

use DiyFormBundle\Controller\Admin\DiyFormRecordCrudController;
use DiyFormBundle\Entity\Record;
use PHPUnit\Framework\TestCase;

class DiyFormRecordCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn_返回正确的实体类名()
    {
        $this->assertEquals(Record::class, DiyFormRecordCrudController::getEntityFqcn());
    }
}