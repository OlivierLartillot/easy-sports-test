<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Team;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
         /*    ->add('role') */
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function($user){
                    return $user->getFirstname().' '.$user->getLastname();
                },    
            ])

/*             ->add('team', EntityType::class, [
                'class' => Team::class,
                'choice_label' => 'name'
            ]) */
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
