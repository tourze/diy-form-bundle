<?php

namespace DiyFormBundle\Tests\Integration\Controller\Admin;

use DiyFormBundle\Controller\Admin\DiyFormFormCrudController;
use DiyFormBundle\Entity\Form;
use PHPUnit\Framework\TestCase;

class DiyFormFormCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn_返回正确的实体类名()
    {
        $this->assertEquals(Form::class, DiyFormFormCrudController::getEntityFqcn());
    }
}