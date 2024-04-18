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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EmployeInformationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('page_pro')
            ->add('idhal')
            ->add('orcid')
            ->add('mail_secondaire')
            ->add('telephone_secondaire')
            ->add('annee_naissance')
            ->add('redirection_mail')
            ->add('imageFile', VichImageType::class, [
                'label' => 'Photo de profil',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'required' => false,
                'download_label' => false,
                'delete_label' => false,
                'allow_delete' => false,
            ])
            ->add('localisation', CollectionType::class, [
                'entry_type' => LocalisationType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Localisations : ',
            ])
            ->add('groupe_principal', EntityType::class, [
                'class' => Groupes::class,
                'required' => false,
                'choice_label' => 'nom',
            ])
            ->add('groupes_secondaires', CollectionType::class, [
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => Groupes::class,
                    'choice_label' => 'nom',
                    'label' => false
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('referent', EntityType::class, [
                'class' => Employe::class,
                'required' => false,
                'choice_label' => function (Employe $employe) {
                    return $employe->getNom() . ' ' . $employe->getPrenom();
                },
                'label' => 'Référent',
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
