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
            ->add('departement', null, [
                'required' => false,
                'label' => 'Département : '
            ])
            ->add('groupe', null, [
                'required' => false,
                'label' => 'Groupe : '
            ])
            ->add('statut', null, [
                'required' => false,
                'label' => 'Statut : '
            ])
        ;
    }
}
