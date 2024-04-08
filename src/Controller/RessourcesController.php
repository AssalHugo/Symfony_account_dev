<?php

namespace App\Controller;

use App\Entity\GroupesSys;
use App\Entity\ResStockagesHome;
use App\Entity\ResStockageWork;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class RessourcesController extends AbstractController
{
    #[Route('/ressources', name: 'ressources')]
    public function index(EntityManagerInterface $em, ChartBuilderInterface $chartBuilder, Request $request): Response {

        //On récupère les ressources de l'utilisateur connecté
        $user = $this->getUser();

        $resStockagesRepo = $em->getRepository(ResStockagesHome::class);

        $resStockages = $resStockagesRepo->findBy(['user' => $user]);

        //On récupere la derniere mesure de chaque ressource
        $mesureDeChaqueRes = [];
        $pourcentage = [];
        foreach($resStockages as $resStockage){
            $mesure = $resStockage->getMesures()->last();
            //On calcule le pourcentage deux chiffres après la virgule entre la valeur actuelle et la valeur max
            $pourcentage[] = round(($mesure->getValeurUse() / $mesure->getValeurMax()) * 100, 2);

            $mesureDeChaqueRes[] = $mesure;
        }

        //Partie Work
        //On récupère les ressources de l'utilisateur connecté
        $resStockagesRepo = $em->getRepository(ResStockageWork::class);

        $GroupesSysRepo = $em->getRepository(GroupesSys::class);

        //On récupère tous les ResStockageWork ou l'utilisateur connecté appartient au groupeSys
        $groupesSys = $GroupesSysRepo->findBy(['groupe' => $user->getGroupeSec

        //---------------------------------Graphique---------------------------------
        $dataSet = [];
        $labels = [];
        //On crée un graphique contenant les mesures de chaque ressource de l'utilisateur connecté ces 30 derniers jours
        foreach($resStockages as $resStockage){

            $mesures = $resStockage->getMesures();
            $data = [];

            foreach($mesures as $mesure){
                $data[] = $mesure->getValeurUse();

                //On stocke les dates des mesures dans un tableau si elles ne sont pas déjà stockées
                if(!in_array($mesure->getDateMesure()->format('d/m/Y H:i:s'), $labels)){
                    $labels[] = $mesure->getDateMesure()->format('d/m/Y H:i:s');
                }
            }

            //On génère une couleur aléatoire hexadécimale pour chaque ressource, qu'on stocke en session pour ne pas que la couleur change à chaque rafraichissement de la page
            $session = $request->getSession();

            //Si la couleur liée à la ressource n'existe pas en session, on la génère
            if(!$session->has('color_'.$resStockage->getId())){
                $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
                $session->set('color_'.$resStockage->getId(), $color);
            }else{
                $color = $session->get('color_'.$resStockage->getId());
            }

            //On stocke les données de chaque ressource dans un tableau
            $dataSet[] = [
                'label' => $resStockage->getNom(),
                'borderColor' => $color,
                'data' => $data,
            ];

        }

        //On crée le graphique
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => $labels,
            'datasets' => $dataSet,
        ]);


        return $this->render('ressources/index.html.twig', [
            'resStockages' => $resStockages,
            'mesures' => $mesureDeChaqueRes,
            'pourcentage' => $pourcentage,
            'chart' => $chart,
        ]);
    }
}
