<?php

namespace App\Controller\Admin;

use App\Entity\Travel;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TravelCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Travel::class;
    }

    // name
    // city
    // country
    // date
    // environment

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('city'),
            TextField::new('country'),
            DateField::new('date')->setFormat('yyyy-MM-dd')->setRequired(true),            
            AssociationField::new('environment', 'Environment')->setRequired(true),
            ImageField::new('path')
                ->setBasePath('/')
                ->setLabel('Image')
                ->setUploadDir('public/')
        ];
    }
}
