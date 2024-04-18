<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactSuppportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('corps', TextareaType::class, [
                'label' => ' ',
                //On met du texte d'aide dans le champ de saisie, on modifie également la taille du champ
                'attr' => ['placeholder' => 'Saisir votre question ou votre problème ici...', 'rows' => 10, 'cols' => 50]
            ])
        ;
    }
}
