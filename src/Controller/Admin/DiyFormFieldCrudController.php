<?php

namespace DiyFormBundle\Controller\Admin;

use DiyFormBundle\Entity\Field;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DiyFormFieldCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Field::class;
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
