<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('email'),
            TextField::new('password')
                ->onlyOnForms()
                ->formatValue(function ($value, $entity) {
                    if ($entity->getId() === null) {
                        $hashedPassword = $this->get('security:password_hashers')>encodePassword($entity, $value);
                        $entity->setPassword($hashedPassword);
                    }
                    return $hashedPassword;
                }),
            ArrayField::new('roles')->setLabel('Roles'),
        ];
    }
}
