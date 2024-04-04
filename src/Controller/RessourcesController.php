<?php

namespace App\Controller;

use App\Entity\ResStockagesHome;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RessourcesController extends AbstractController
{
    #[Route('/ressources', name: 'ressources')]
    public function index(EntityManagerInterface $em): Response{

        //On récupère les ressources de l'utilisateur connecté
        $user = $this->getUser();

        $resStockagesRepo = $em->getRepository(ResStockagesHome::class);

        $resStockages = $resStockagesRepo->findBy(['user' => $user]);

        //On récupere la derniere mesure de chaque ressource
        $mesures = [];
        $pourcentage = [];
        foreach($resStockages as $resStockage){
            $mesure = $resStockage->getMesures()->last();
            //On calcule le pourcentage deux chiffres après la virgule entre la valeur actuelle et la valeur max
            $pourcentage[] = round(($mesure->getValeurUse() / $mesure->getValeurMax()) * 100, 2);

            $mesures[] = $mesure;
        }

        return $this->render('ressources/index.html.twig', [
            'resStockages' => $resStockages,
            'mesures' => $mesures,
            'pourcentage' => $pourcentage
        ]);
    }
}
