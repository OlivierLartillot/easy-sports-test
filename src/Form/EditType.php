<?php

namespace App\Form;

use App\Entity\Club;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\DateType as TypeDateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\File;

class EditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstname', TextType::class, [
            'label' => 'Prénom*'
        ] )
        ->add('lastname', TextType::class, [
            'label' => 'Nom*'
        ])
        ->add('birthdate', TypeDateType::class,[
            'label' => 'Date de naissance*',
            'years' => range(1950,2050)
        ])
        ->add('email', EmailType::class, [
            'required' => true,
            'label' => 'Email*'
        ])
        
        ->add('roles', ChoiceType::class, [
            'label' => 'Etes-vous ?',
            'choices' => [
                'Joueur' => 'ROLE_PLAYER',
                'Entraineur' => 'ROLE_COACH'
            ],
            'multiple' => true,
            'expanded' => true
        ])     
        
         ->add('status', HiddenType::class, [
            'data' => '1',
            ])

        ->add('picture', FileType::class,[
            'label' => 'Ton avatar ou photo (jpg, png)',
            'mapped' => false,
            'required' => false,
            /*'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Merci d\'insérer un fichier jpg ou png',
                ])
            ], */
            'data_class' => null,

        ])
        ->add('city', TextType::class, [
            'label' => 'Ville',
            'required' =>false,
        ])
        ->add('club', EntityType::class,[
            'class' => Club::class,
            'label'=> 'Ton club',
            'placeholder'=>'Choisis ton club',
            'choice_label' => function($club){
                return $club->getName();
            },

            'multiple' => false,
            'expanded' => false,
            'required' => false
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
