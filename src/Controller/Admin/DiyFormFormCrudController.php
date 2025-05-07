<?php

namespace DiyFormBundle\Controller\Admin;

use DiyFormBundle\Entity\Form;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class DiyFormFormCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Form::class;
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
