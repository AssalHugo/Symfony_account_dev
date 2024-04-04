<?php

namespace App\Service;

use App\Entity\Employe;
use App\Entity\Groupes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class Remove
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * Fonction qui permet de supprimer un employé d'un groupe
     * @param int $idEmploye
     * @param int $idGroupe
     * @return void
     */
    public function supprimerEmployeDuGroupeSecond(int $idEmploye, int $idGroupe, Request $request) : void {

        $employeRepo = $this->entityManager->getRepository(Employe::class);
        $employe = $employeRepo->find($idEmploye);

        $groupeRepo = $this->entityManager->getRepository(Groupes::class);
        $groupe = $groupeRepo->find($idGroupe);

        $employe->removeGroupesSecondaire($groupe);
        $this->entityManager->persist($employe);
        $this->entityManager->flush();

        //Si le groupe est le groupe principal de l'employé, on affiche un message d'erreur
        if($employe->getGroupePrincipal()->getId() == $groupe->getId()){
            $session = $request->getSession();
            $session->getFlashBag()->add('message', "Impossible de supprimer le groupe principal");
            $session->set('statut', 'danger');
        }
        else {
            $session = $request->getSession();
            $session->getFlashBag()->add('message', "L'employé a été supprimé du groupe");
            $session->set('statut', 'success');
        }
    }
}