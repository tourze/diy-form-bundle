<?php

namespace DiyFormBundle\Tests\Integration\Controller\Admin;

use DiyFormBundle\Controller\Admin\DiyFormFieldCrudController;
use DiyFormBundle\Entity\Field;
use PHPUnit\Framework\TestCase;

class DiyFormFieldCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn_返回正确的实体类名()
    {
        $this->assertEquals(Field::class, DiyFormFieldCrudController::getEntityFqcn());
    }
}