<?php

namespace App\Form;

use App\Entity\Employe;
use App\Entity\EtatsRequetes;
use App\Entity\Groupes;
use App\Entity\Localisations;
use App\Entity\Requetes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequeteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('mail')
            ->add('commentaire')
            ->add('groupe_principal', EntityType::class, [
                'class' => Groupes::class,
                'choice_label' => 'id',
            ])
            ->add('localisation', EntityType::class, [
                'class' => Localisations::class,
                'choice_label' => 'id',
            ])
            ->add('referent', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => 'id',
            ])
            ->add('etat_requete', EntityType::class, [
                'class' => EtatsRequetes::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Requetes::class,
        ]);
    }
}
