<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\Employe;
use App\Entity\Groupes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjouterGroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('acronyme')
            ->add('responsable', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => function ($employe) {
                    return $employe->getPrenom() . ' ' . $employe->getNom();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Groupes::class,
        ]);
    }
}
