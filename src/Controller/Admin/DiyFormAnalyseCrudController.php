<?php

namespace DiyFormBundle\Controller\Admin;

use DiyFormBundle\Entity\Analyse;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DiyFormAnalyseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Analyse::class;
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
