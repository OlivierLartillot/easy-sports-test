<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {

        //tableau des champs :
        $fields = [];
        
            $fields [] = yield TextField::new('email')->hideOnIndex();
            $fields [] = yield TextField::new('password', 'Mot de passe : A cacher en production, et enlever le password type')->hideOnIndex()->hideOnDetail()->setFormType(PasswordType::class);
            
            //exemple pour afficher en fonction de la page en cours
            // if page index alors tu affiche Lastname en premier sinon tu les inverses
            // ca tombe bien sur l index on tri le lastname par ordre ASC (voir plus bas "configure crud)
            if ($pageName == Crud::PAGE_INDEX) {
                $fields [] = yield TextField::new('Lastname', 'Nom');
                $fields [] = yield TextField::new('Firstname', 'Prénom');
            } 
            else {
                $fields [] = yield TextField::new('Firstname', 'Prénom');
                $fields [] = yield TextField::new('Lastname', 'Nom');
            }
            
            

            $fields [] = yield DateField::new('birthdate', 'Date de naissance');
            $fields [] = yield ImageField::new('picture', 'Image')->hideOnIndex()->setUploadDir('public/uploads/images/users');


            $fields [] = yield ChoiceField::new('roles', 'Role(s)')->setChoices([
                'Admin'  => 'ROLE_ADMIN',
                'Coach' => 'ROLE_COACH',
                'Joueur' => 'ROLE_PLAYER',
            ])->renderExpanded()->allowMultipleChoices();


            $fields [] = yield ChoiceField::new('status', 'Valide/Banni')->setChoices([
                'Valide' => '1',
                'Compte suspendus' => '0',
            ]);

            $fields [] = yield TextField::new('slug')->hideOnIndex()->hideOnForm();

            return $fields;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
          ->setDefaultSort(['lastname' => 'ASC']);
    } 


}
