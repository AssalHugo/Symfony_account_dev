<?php

namespace App\Controller;

use App\Entity\ResStockagesHome;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class RessourcesController extends AbstractController
{
    #[Route('/ressources', name: 'ressources')]
    public function index(EntityManagerInterface $em, ChartBuilderInterface $chartBuilder): Response {

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

        //---------------------------------Graphique---------------------------------
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
//
        //On crée un graphique contenant les mesures de chaque ressource de l'utilisateur connecté ces 30 derniers jours
        foreach($resStockages as $resStockage){
            $mesures = $resStockage->getMesures();
            $data = [];
            $labels = [];
            foreach($mesures as $mesure){
                $data[] = $mesure->getValeurUse();
                $labels[] = $mesure->getDateMesure()->format('d/m/Y');
            }
            $chart->setData([
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => $resStockage->getNom(),
                        'borderColor' => 'rgb(255, 99, 132)',
                        'data' => $data,
                    ],
                ],
            ]);
        }
//
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ]);

        return $this->render('ressources/index.html.twig', [
            'resStockages' => $resStockages,
            'mesures' => $mesures,
            'pourcentage' => $pourcentage,
            'chart' => $chart,
        ]);
    }
}
