<?php

namespace App\Form;

use App\Entity\Contrats;
use App\Entity\Employe;
use App\Entity\Status;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'type',
                'label' => 'Statut : '
            ])
            ->add('date_debut', null, [
                'widget' => 'single_text',
                'label' => 'Date de début : '
            ])
            ->add('date_fin', null, [
                'widget' => 'single_text',
                'label' => 'Date de fin : '
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrats::class,
        ]);
    }
}
