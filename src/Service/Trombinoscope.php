<?php

namespace App\Service;

use App\Repository\EmployeRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class Trombinoscope
{

    /**
     * @param FormInterface $formFiltre
     * @param Request $request
     * @param EmployeRepository $employeRepository
     * @return QueryBuilder
     */
    public function getQuery(FormInterface $formFiltre, Request $request, EmployeRepository $employeRepository) : QueryBuilder {

        //Si le formulaire est soumis et valide
        if ($formFiltre->isSubmitted() && $formFiltre->isValid()) {
            //On vide tout le GET
            $request->query->replace([]);

            //On récupère les données du formulaire
            $data = $formFiltre->getData();
            //On récupère le département, le groupe et le statut
            $nom = $data['nom'];
            $prenom = $data['prenom'];
            $departement = $data['departement'];
            $groupe = $data['groupe'];
            $statut = $data['statut'];

            //On récupère les utilisateurs en fonction des filtres
            $query = $employeRepository->findByFiltre($nom, $prenom, $departement, $groupe, $statut);

            //On met les informations dans la session pour les garder en mémoire
            $session = $request->getSession();
            $session->set('nom', $nom);
            $session->set('prenom', $prenom);
            $session->set('departement', $departement);
            $session->set('groupe', $groupe);
            $session->set('statutEmploye', $statut);

        } //Sinon si le GET contient des filtres
        else if ($request->query->get('nom') != null || $request->query->get('prenom') != null || $request->query->get('departement') != null || $request->query->get('groupe') != null || $request->query->get('statut') != null) {

            //On vide la session
            $session = $request->getSession();
            $session->remove('nom');
            $session->remove('prenom');
            $session->remove('departement');
            $session->remove('groupe');
            $session->remove('statutEmploye');

            //On récupère les utilisateurs en fonction des filtres
            if ($request->query->get('departement') != null) {
                $departement = $request->query->get('departement');
            } else {
                $departement = "";
            }

            if ($request->query->get('groupe') != null) {
                $groupe = $request->query->get('groupe');
            } else {
                $groupe = "";
            }

            if ($request->query->get('statut') != null) {
                $statut = $request->query->get('statut');
            } else {
                $statut = "";
            }

            $query = $employeRepository->findByFiltreId($departement, $groupe, $statut);
        }//Sinon si les filtre sont dans la session
        else if ($request->getSession()->get('nom') != null || $request->getSession()->get('prenom') != null || $request->getSession()->get('departement') != null || $request->getSession()->get('groupe') != null || $request->getSession()->get('statutEmploye') != null) {
            //On récupère les utilisateurs en fonction des filtres
            $session = $request->getSession();
            $nom = $session->get('nom');
            $prenom = $session->get('prenom');
            $departement = $session->get('departement');
            $groupe = $session->get('groupe');
            $statut = $session->get('statutEmploye');

            $query = $employeRepository->findByFiltre($nom, $prenom, $departement, $groupe, $statut);
        } //Sinon on récupère tous les utilisateurs
        else {

            //On récupère tous les utilisateurs
            $query = $employeRepository->findAllEmployes();
        }

        return $query;
    }

    public function setNbGroupesAffichesEtNbDepAffiches($queryResult, int &$nbGroupesAffiches, int &$nbDepartementsAffiches) {

        $groupes = [];
        $departements = [];
        foreach ($queryResult as $employe) {

            $groupePrincipal = $employe->getGroupePrincipal();
            if ($groupePrincipal != null && !in_array($groupePrincipal, $groupes)) {
                $nbGroupesAffiches++;
                //On regarde si le département du groupe principal est déjà dans le tableau
                if ($groupePrincipal->getDepartement() != null && !in_array($groupePrincipal->getDepartement(), $departements)) {
                    $nbDepartementsAffiches++;
                }
            }
            $groupes[] = $groupePrincipal;
            $departements[] = $groupePrincipal->getDepartement();

            //Pour chaque groupes secondaires de l'employé, on vérifie si il est déjà dans le tableau
            foreach ($employe->getGroupesSecondaires() as $groupe) {
                if ($groupe != null && !in_array($groupe, $groupes)) {
                    $nbGroupesAffiches++;
                    if ($groupe->getDepartement() != null && !in_array($groupe->getDepartement(), $departements)) {
                        $nbDepartementsAffiches++;
                    }
                }
                $groupes[] = $groupe;
                $departements[] = $groupe->getDepartement();
            }
        }
    }
}