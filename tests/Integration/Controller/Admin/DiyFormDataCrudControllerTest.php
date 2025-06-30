<?php

namespace DiyFormBundle\Tests\Integration\Controller\Admin;

use DiyFormBundle\Controller\Admin\DiyFormDataCrudController;
use DiyFormBundle\Entity\Data;
use PHPUnit\Framework\TestCase;

class DiyFormDataCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn_返回正确的实体类名()
    {
        $this->assertEquals(Data::class, DiyFormDataCrudController::getEntityFqcn());
    }
}