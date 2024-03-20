<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrombinoscopeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //Aucun des champs n'est obligatoire
        $builder
            ->add('departement', null, ['required' => false])
            ->add('groupe', null, ['required' => false])
            ->add('statut', null, ['required' => false])
        ;
    }
}
