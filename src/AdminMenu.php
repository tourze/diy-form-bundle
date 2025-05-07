<?php

namespace DiyFormBundle;

use DiyFormBundle\Entity\Form;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Attribute\MenuProvider;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

#[MenuProvider]
class AdminMenu
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (!$item->getChild('装修中心')) {
            $item->addChild('装修中心');
        }
        $item->getChild('装修中心')->addChild('表单装修')->setUri($this->linkGenerator->getCurdListPage(Form::class));
    }
}
