<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\RequetesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ApiController extends AbstractController
{
    #[Route('/api/demandes', name: 'api_demandes_en_cours')]
    #[IsGranted('ROLE_API')]
    public function getDemandesEnCours(RequetesRepository $repository): JsonResponse
    {
        $demandes = $repository->findByEtatRequete('Validé par admin');

        $data = [];
        foreach ($demandes as $demande) {
            $data[] = [
                'id' => $demande->getId(),
                'nom' => $demande->getNom(),
                'prenom' => $demande->getPrenom(),
                'date_de_debut_de_contrat' => $demande->getContrat()->getDateDebut()->format('Y-m-d'),
                'date_de_fin_de_contrat' => $demande->getContrat()->getDateFin()->format('Y-m-d'),
                'groupe_principal' => $demande->getGroupePrincipal()->getNom(),
            ];
        }

        return new JsonResponse($data);
    }
}
