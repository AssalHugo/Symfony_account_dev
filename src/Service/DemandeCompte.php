<?php

namespace App\Service;

use App\Entity\EtatsRequetes;
use App\Entity\Requetes;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class DemandeCompte {

    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * Fonction permettant d'accepter une demande de compte
     * @param $id
     * @param Request $request
     * @return void
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function refuserDemandeCompte($id, Request $request) : void {

        //On récupère la demande de compte
        $requetesRepo = $this->entityManager->getRepository(Requetes::class);

        $demandeCompte = $requetesRepo->find($id);

        //On récupere l'objet EtatsRequetes avec l'état 'Refusé par admin'
        $etatRequeteRepo = $this->entityManager->getRepository(EtatsRequetes::class);

        $etatRequete = $etatRequeteRepo->findOneBy(['etat' => 'Refusé']);

        //On change le statut de la demande de compte
        $demandeCompte->setEtatRequete($etatRequete);

        $this->entityManager->persist($demandeCompte);
        $this->entityManager->flush();

        //On crée un message flash pour informer l'utilisateur que la demande a bien été refusée
        $session = $request->getSession();
        $session->getFlashBag()->add('message', "L'utilisateur a bien été refusé.");
        $session->set('statut', 'success');
    }

}