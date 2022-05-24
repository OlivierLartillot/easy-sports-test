<?php

namespace App\Form;

use App\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', ChoiceType::class, [
                'choices'  => [
                    'Equipe 1' =>'Equipe 1',
                    'Equipe 2' =>'Equipe 2',
                    'Equipe 3' =>'Equipe 3',
                    'Equipe 4' =>'Equipe 4',
                    'Equipe 5' =>'Equipe 5',
                ],
            ])
            ->add('ageCategory')
            ->add('status', HiddenType::class, [
                'data' => '1',
            ])
            ->add('city')
            /* ->add('club') */
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
