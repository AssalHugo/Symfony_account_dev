<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangerRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'label' => 'Nom d\'utilisateur : ',
                'attr' => ['id' => 'user_select'],
                'autocomplete' => true
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'RH' => 'ROLE_RH',
                    'Administrateur' => 'ROLE_ADMIN',
                    'API' => 'ROLE_API',
                    'API_MDP' => 'ROLE_API_MDP'
                ],
                'label' => 'Rôle : ',
                'attr' => ['id' => 'role_select']
            ]);
    }
}
