<?php

namespace App\Form;

use App\Entity\Employe;
use App\Entity\EtatsRequetes;
use App\Entity\Groupes;
use App\Entity\Localisations;
use App\Entity\Requetes;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequeteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class ,[
                'label' => 'Prénom : '
            ])
            ->add('nom' , TextType::class, [
                'label' => 'Nom : '
            ])
            ->add('mail' , TextType::class, [
                'label' => 'Mail : '
            ])
            ->add('telephone' , TextType::class, [
                'label' => 'Téléphone : '
            ])
            //On ajoute les champs statut, date arrivée et date départ dans le formulaire qui vont créer un Contrat
            ->add('contrat', ContratType::class, [
                'label' => 'Contrat : '
            ]) //On appelle le formulaire ContratType deja créé
            ->add('localisation', LocalisationType::class, [
                'label' => 'Localisation : '
            ])//On appelle le formulaire LocalisationType deja créé
            ->add('groupe_principal', EntityType::class, [
                'class' => Groupes::class,
                'choice_label' => 'nom',
                'label' => 'Groupe principal : '
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire (Si informations à rajouter) : ',
                'required' => false
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
