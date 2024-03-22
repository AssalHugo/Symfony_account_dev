<?php

namespace App\Controller;

use App\Entity\EtatsRequetes;
use App\Entity\Requetes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RhController extends AbstractController
{
    #[Route('/rh/listeDemandesComptes', name: 'listeDemandesComptes')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        //On récupère les demandes de comptes ou le statut est 'Demandé'
        $requetesRepo = $entityManager->getRepository(Requetes::class);

        $demandesComptes = $requetesRepo->findByEtatRequete('Demandé');

        return $this->render('rh/demandesCompte.html.twig', [
            'demandes' => $demandesComptes,
        ]);
    }

    #[Route('/rh/validerDemandeCompte/{id}', name: 'validerDemandeCompte')]
    public function validerDemandeCompte($id, EntityManagerInterface $entityManager, Request $request): Response
    {
        //On récupère la demande de compte
        $requetesRepo = $entityManager->getRepository(Requetes::class);

        $demandeCompte = $requetesRepo->find($id);

        //On récupere l'objet EtatsRequetes avec l'état 'Validé par admin'
        $etatRequeteRepo = $entityManager->getRepository(EtatsRequetes::class);

        $etatRequete = $etatRequeteRepo->findOneBy(['etat' => 'Validé par RH']);

        //On change le statut de la demande de compte
        $demandeCompte->setEtatRequete($etatRequete);

        $entityManager->persist($demandeCompte);
        $entityManager->flush();

        //On crée un message flash pour informer l'utilisateur que la demande a bien été validée
        $session = $request->getSession();
        $session->getFlashBag()->add('message', "L'utilisateur a bien été validé.");
        $session->set('statut', 'success');

        return $this->redirectToRoute('listeDemandesComptes');
    }

    #[Route('/rh/refuserDemandeCompte/{id}', name: 'refuserDemandeCompte')]
    public function refuserDemandeCompte($id, EntityManagerInterface $entityManager, Request $request): Response
    {
        //On récupère la demande de compte
        $requetesRepo = $entityManager->getRepository(Requetes::class);

        $demandeCompte = $requetesRepo->find($id);

        //On récupere l'objet EtatsRequetes avec l'état 'Refusé par admin'
        $etatRequeteRepo = $entityManager->getRepository(EtatsRequetes::class);

        $etatRequete = $etatRequeteRepo->findOneBy(['etat' => 'Refusé']);

        //On change le statut de la demande de compte
        $demandeCompte->setEtatRequete($etatRequete);

        $entityManager->persist($demandeCompte);
        $entityManager->flush();

        //On crée un message flash pour informer l'utilisateur que la demande a bien été refusée
        $session = $request->getSession();
        $session->getFlashBag()->add('message', "L'utilisateur a bien été refusé.");
        $session->set('statut', 'success');

        return $this->redirectToRoute('listeDemandesComptes');
    }
}
