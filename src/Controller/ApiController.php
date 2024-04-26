<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\EtatSystemeRequete;
use App\Repository\RequetesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ApiController extends AbstractController
{
    #[Route('/api/demandes', name: 'api_demandes_en_cours')]
    #[IsGranted('ROLE_API')]
    public function getDemandesEnCours(RequetesRepository $repository, EntityManagerInterface $em, Request $request): JsonResponse
    {
        //Si le POST contient la réponse de l'admin, on met à jour l'état de la requête
        if ($request->getMethod() === 'PUT') {
            return $this->updateRequete($repository, $em, $request);
        }

        $demandes = $repository->findByEtatRequete('Validé par admin');

        $data = [];
        foreach ($demandes as $demande) {
            $data[] = [
                'id' => $demande->getId(),
                'login' => $demande->getUserCree()->getUsername(),
                'nom' => $demande->getNom(),
                'prenom' => $demande->getPrenom(),
                'date_de_debut_de_contrat' => $demande->getContrat()->getDateDebut()->format('Y-m-d'),
                'date_de_fin_de_contrat' => $demande->getContrat()->getDateFin()->format('Y-m-d'),
                'groupe_principal' => $demande->getGroupePrincipal()->getNom(),
                'etat_Système' => $demande->getEtatSystemeRequete()->getEtat(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/api/demandesMdp', name: 'api_demandesMdp_en_cours')]
    #[IsGranted('ROLE_API_MDP')]
    public function getDemandesMDPEnCours(RequetesRepository $repository, EntityManagerInterface $em, Request $request): JsonResponse
    {
        if ($request->getMethod() === 'PUT') {
            return $this->updateRequete($repository, $em, $request);
        }
        $demandes = $repository->findByEtatRequete('Validé par admin');

        $data = [];
        foreach ($demandes as $demande) {
            $data[] = [
                'id' => $demande->getId(),
                'login' => $demande->getUserCree()->getUsername(),
                'nom' => $demande->getNom(),
                'prenom' => $demande->getPrenom(),
                'date_de_debut_de_contrat' => $demande->getContrat()->getDateDebut()->format('Y-m-d'),
                'date_de_fin_de_contrat' => $demande->getContrat()->getDateFin()->format('Y-m-d'),
                'groupe_principal' => $demande->getGroupePrincipal()->getNom(),
                'mdp' => $demande->getMdpProvisoire(),
                'etat_Système' => $demande->getEtatSystemeRequete()->getEtat(),
            ];
        }

        return new JsonResponse($data);
    }

    private function updateRequete(RequetesRepository $repository, EntityManagerInterface $em, Request $request) : JsonResponse{
        $data = json_decode($request->getContent(), true);
        //On modifie l'état systeme des requete d'ont l'id est la clé de la réponse
        foreach ($data as $id => $reponse) {
            $requete = $repository->find($id);
            //on récupere l'état système correspondant à la réponse
            $etatSysteme = $em->getRepository(EtatSystemeRequete::class)->findOneBy(['etat' => $reponse]);
            $requete->setEtatSystemeRequete($etatSysteme);
            $em->persist($requete);
        }
        $em->flush();
        //On renvoie un message de succès
        return new JsonResponse(['message' => "Mise à jour effectuée sur les requêtes : " . implode(', ', array_keys($data))]);
    }
}
