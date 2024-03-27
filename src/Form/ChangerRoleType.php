<?php

namespace App\Form;

use App\Entity\Employe;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangerRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'label' => 'Nom d\'utilisateur : '
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Responsable de Groupe' => 'ROLE_RESPONSABLE_GROUPE',
                    'RH' => 'ROLE_RH',
                    'Administrateur' => 'ROLE_ADMIN',

                ],
                'label' => 'Rôle : '
            ])
        ;
    }
}
