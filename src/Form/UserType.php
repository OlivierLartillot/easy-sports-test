<?php

namespace App\Form;

use App\Entity\Club;
use App\Entity\User;
use Doctrine\DBAL\Types\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType as TypeDateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
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
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe*'],
                'second_options' => ['label' => 'Confirmez le mot de passe*'],

                'constraints' => [
                        new NotBlank([
                            'message' => 'Un mot de passe est nécessaire',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
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
            /*->add('slug') */
            ->add('picture', FileType::class,[
                'label' => 'Ton avatar ou photo (jpg, png)',
                'mapped' => false,
                'required' => false,
                /* 'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Merci d\'insérer un fichier jpg ou png',
                    ])
                ], */

            ])
            /*  ->add('city')
            ->add('club', EntityType::class,[
                'class' => Club::class,
                'choice_label'=> 'name',

                'multiple' => false,
                'expanded' => true,
            ]) */
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
