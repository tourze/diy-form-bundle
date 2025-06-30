<?php

namespace DiyFormBundle\Tests\Integration\Controller\Admin;

use DiyFormBundle\Controller\Admin\DiyFormOptionCrudController;
use DiyFormBundle\Entity\Option;
use PHPUnit\Framework\TestCase;

class DiyFormOptionCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn_返回正确的实体类名()
    {
        $this->assertEquals(Option::class, DiyFormOptionCrudController::getEntityFqcn());
    }
}