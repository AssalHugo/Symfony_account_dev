<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\Employe;
use App\Entity\Groupes;
use App\Entity\Localisations;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeInformationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('photo')
            ->add('page_pro')
            ->add('idhal')
            ->add('orcid')
            ->add('mail_secondaire')
            ->add('telephone_secondaire')
            ->add('annee_naissance')
            ->add('redirection_mail')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'required' => false,
            ])
            ->add('localisation', EntityType::class, [
                'class' => Localisations::class,
                'choice_label' => 'bureau',
                'required' => false,
                'multiple' => true,
            ])
            ->add('adjoint_de', EntityType::class, [
                'class' => Groupes::class,
                'choice_label' => 'nom',
                'label' => 'Adjoint du groupe : ',
                'required' => false,
                'multiple' => true,
            ])
            ->add('groupes_secondaires', EntityType::class, [
                'class' => Groupes::class,
                'choice_label' => 'nom',
                'required' => false,
                'multiple' => true,
            ])
            ->add('groupe_principal', EntityType::class, [
                'class' => Groupes::class,
                'required' => false,
                'choice_label' => 'id',
            ])
            ->add('responsable_departement', EntityType::class, [
                'class' => Departement::class,
                'required' => false,
                'choice_label' => 'id',
            ])
            ->add('referent', EntityType::class, [
                'class' => Employe::class,
                'required' => false,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employe::class,
        ]);
    }
}
