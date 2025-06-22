<?php

namespace DiyFormBundle\Service;

use DiyFormBundle\Controller\SqlController;
use DiyFormBundle\Controller\TestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Loader\AttributeClassLoader;
use Symfony\Component\Routing\RouteCollection;

class AttributeControllerLoader extends AbstractController
{
    public function __construct(private AttributeClassLoader $controllerLoader)
    {
    }

    public function autoload(): RouteCollection
    {
        $collection = new RouteCollection();
        $collection->addCollection($this->controllerLoader->load(SqlController::class));
        $collection->addCollection($this->controllerLoader->load(TestController::class));
        $collection->addCollection($this->controllerLoader->load(AttributeControllerLoader::class));
        
        return $collection;
    }

    #[Route('/diy-form-bundle')]
    public function __invoke(): void
    {
        // This service exists to satisfy PHPStan requirements for controllers with attributes
    }
}