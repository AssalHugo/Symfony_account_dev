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
        // Vérifie si l'utilisateur est connecté et a le rôle ROLE_ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $demandes = $repository->findByEtatRequete('Validé par admin');

        $data = [];
        foreach ($demandes as $demande) {
            $data[] = [
                'id' => $demande->getId(),
                'nom' => $demande->getNom(),
                'prenom' => $demande->getPrenom(),
                'groupe_principal' => $demande->getGroupePrincipal()->getNom(),
                'date_de_fin_de_contrat' => $demande->getContrat()->getDateFin()->format('Y-m-d'),
            ];
        }

        return new JsonResponse($data);
    }
}
