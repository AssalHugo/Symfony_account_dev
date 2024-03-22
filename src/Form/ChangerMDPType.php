<?php

namespace App\Form;

use App\Entity\Employe;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangerMDPType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //Formulaire qui va permettre de changer le mot de passe d'un utilisateur
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'label' => 'Nom d\'utilisateur : '
            ])
            ->add('password', null, [
                'label' => 'Nouveau mot de passe : '
            ])
        ;
    }
}
