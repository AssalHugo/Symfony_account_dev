<?php

namespace App\Form;

use App\Entity\Employe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrombinoscopeType extends AbstractType
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();

        //On récupère les valeurs des filtres en sessions
        $nom = $session->get('nom');
        $prenom = $session->get('prenom');
        $departement = $session->get('departement');
        $groupe = $session->get('groupe');
        $statut = $session->get('statutEmploye');

        //Aucun des champs n'est obligatoire on initialise les valeurs aux valeurs des filtres en sessions si elles existent
        $builder
            ->add('nom', null, [
                'required' => false,
                'label' => 'Nom : ',
                'data' => $prenom
            ])
            ->add('prenom', null, [
                'required' => false,
                'label' => 'Prénom : ',
                'data' => $prenom
            ])
            ->add('departement', null, [
                'required' => false,
                'label' => 'Département : ',
                'data' => $departement
            ])
            ->add('groupe', null, [
                'required' => false,
                'label' => 'Groupe : ',
                'data' => $groupe
            ])
            ->add('statut', null, [
                'required' => false,
                'label' => 'Statut : ',
                'data' => $statut
            ])
        ;
    }
}