<?php

namespace App\Form;

use App\Entity\Result;
use App\Entity\Team;
use App\Entity\User;
use App\Repository\TeamRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ResultType extends AbstractType
{   

    private $security;

    public function __construct(Security $security){
        $this->security = $security;
    }
   
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        $user = $this->security->getUser();
        $builder
        ->add('team', EntityType::class,[
            'class'=>Team::class,
            'choice_label' => 'nameAgeCategory',
            'label' => 'Equipe',
            'mapped' => false,
            'placeholder' => 'Choisissez une équipe',
            'query_builder' => function (TeamRepository $teamRepository) use ($user) {
                $team = $teamRepository->createTeamFromUserQueryBuilder($user);
                return $team ;
            }
        ])
        ->add('user', EntityType::class,[
            'label' => 'Joueur',
            'class'=>User::class,
            'choice_label' => 'firstname',
            'label_attr' => array('class' => 'd-none user_label'), # grâce à BS
            'attr' => array('class'=>'d-none')
        ])
        ->add('result', TextType::class,[
            'label' => 'Résultat',
            'label_attr' => array('class' => 'd-none result_label'),
            'attr' => array('class'=>'d-none')
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Result::class,
        ]);
    }
}
