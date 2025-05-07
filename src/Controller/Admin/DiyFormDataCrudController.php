<?php

namespace DiyFormBundle\Controller\Admin;

use DiyFormBundle\Entity\Data;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DiyFormDataCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Data::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
