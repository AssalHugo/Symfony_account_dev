<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
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

        $departement = $session->get('departement');
        $groupe = $session->get('groupe');
        $statut = $session->get('statut');

        //Aucun des champs n'est obligatoire on initialise les valeurs aux valeurs des filtres en sessions si elles existent
        $builder
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