<?php

namespace DiyFormBundle\Tests\Integration\Controller\Admin;

use DiyFormBundle\Controller\Admin\DiyFormAnalyseCrudController;
use DiyFormBundle\Entity\Analyse;
use PHPUnit\Framework\TestCase;

class DiyFormAnalyseCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn_返回正确的实体类名()
    {
        $this->assertEquals(Analyse::class, DiyFormAnalyseCrudController::getEntityFqcn());
    }
}