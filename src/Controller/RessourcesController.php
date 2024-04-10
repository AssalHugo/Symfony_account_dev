<?php

namespace App\Controller;

use App\Entity\GroupesSys;
use App\Entity\ResStockagesHome;
use App\Entity\ResStockageWork;
use App\Entity\StockagesMesuresHome;
use App\Entity\StockagesMesuresWork;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

        $resStockagesHomeRepository = $em->getRepository(ResStockagesHome::class);
        $resStockagesHome = $resStockagesHomeRepository->findBy(['user' => $user]);
        $mesureDeChaqueResHomeID = $resStockagesHomeRepository->findLatestMeasurementsByUser($user->getId());

        //On récupere la derniere mesure de chaque ressource Home
        $mesureDeChaqueResHome = [];
        $pourcentageHome = [];
        foreach($mesureDeChaqueResHomeID as $mesureResHomeID){
            //On récupère la mesure
            $mesure = $em->getRepository(StockagesMesuresHome::class)->find($mesureResHomeID['id']);

            //On calcule le pourcentage deux chiffres après la virgule entre la valeur actuelle et la valeur max
            $pourcentageHome[] = round(($mesure->getValeurUse() / $mesure->getValeurMax()) * 100, 2);

            $mesureDeChaqueResHome[] = $mesure;
        }



        //Partie Work
        //On récupère les ressources de l'utilisateur connecté
        //On récupère tous les groupesSys de l'utilisateur connecté
        $groupeSys = $em->getRepository(GroupesSys::class)->findBy(['user' => $user]);

        $resStockagesWorkRepo = $em->getRepository(ResStockageWork::class);
        //On récupère les ressources de chaque groupeSys
        $resStockageWork = $resStockagesWorkRepo->findBy(['groupeSys' => $groupeSys]);
        $mesureDeChaqueResWorkID = $resStockagesWorkRepo->findLatestMeasurementsByUser($groupeSys);

        //On récupère la dernière mesure de chaque ressource Work
        $mesureDeChaqueResWork = [];
        $pourcentageWork = [];
        foreach($mesureDeChaqueResWorkID as $mesureResWorkID){
            //On récupère la mesure
            $mesure = $em->getRepository(StockagesMesuresWork::class)->find($mesureResWorkID['id']);

            //On calcule le pourcentage deux chiffres après la virgule entre la valeur actuelle et la valeur max
            $pourcentageWork[] = round(($mesure->getValeurUse() / $mesure->getValeurMax()) * 100, 2);

            $mesureDeChaqueResWork[] = $mesure;
        }

        //On fusionne les deux tableaux de ressources
        $resStockages = array_merge($resStockagesHome, $resStockageWork);

        //---------------------------------Formulaire---------------------------------
        //On crée un formulaire pour permettre de modifier la date de début et de fin des mesures affichées (par jour : 30 derniers jours, par semaines : 1 an, par mois : 5 ans)
        $form = $this->createFormBuilder()
            ->add('periode', ChoiceType::class, [
                'choices' => [
                    '30 derniers jours' => '30 days',
                    '1 an' => '1 year',
                    '5 ans' => '5 years',
                ],
                'label' => 'Période',
                'attr' => [
                    'onchange' => 'this.form.submit()',
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $periode = $form->getData()['periode'];
        }else{
            $periode = '30 days';
        }


        //---------------------------------Graphique---------------------------------
        $dataSet = [];
        $labels = [];

        //Le format change en fonction de la période choisie
        if($periode == '30 days'){
            $format = 'd/m/Y H:i:s';
        }else if($periode == '1 year'){
            $format = 'W/Y';
        }
        else {
            $format = 'Y';
        }

        //On crée un graphique contenant les mesures de chaque ressource de l'utilisateur connecté ces 30 derniers jours
        foreach($resStockages as $resStockage){

            //On récupère les mesures de la ressource qui ont la meme période que celle choisie, et qui sont dans la période choisie
            $mesures = $resStockage->getMesures()->filter(function($mesure) use ($periode){

                $dateMesure = $mesure->getDateMesure();
                $dateActuelle = new \DateTime();

                if($periode == '30 days'){
                    $dateActuelle->modify('-30 days');
                }elseif($periode == '1 year'){
                    $dateActuelle->modify('-1 year');
                }else{
                    $dateActuelle->modify('-5 years');
                }

                return $mesure->getPeriode()->getType() == $periode && $dateMesure >= $dateActuelle;
            });

            $data = [];
            $dateMesure = null;
            foreach($mesures as $mesure){

                //Si il existe une mesure pour la date actuelle, on ajoute une valeur null pour les dates où il n'y a pas de mesure
                if ($dateMesure != null) {
                    $interval = $dateMesure->diff($mesure->getDateMesure());
                    //on modèle l'interval en fonction de la période
                    if($periode == '30 days'){
                        $nbJours = $interval->format('%a');
                    }elseif($periode == '1 year'){
                        $nbJours = $interval->format('%a') / 7;
                    }
                    else {
                        $nbJours = $interval->format('%y');
                    }

                    for($i = 1; $i < $nbJours; $i++){

                        array_unshift($data, null);
                    }
                }

                array_unshift($data, $mesure->getValeurUse() / $mesure->getValeurMax() * 100);

                $dateMesure = $mesure->getDateMesure();
                //On stocke les dates des mesures dans un tableau si elles ne sont pas déjà stockées
                if(!in_array($dateMesure->format($format), $labels)) {

                    $labels[] = $dateMesure->format($format);
                }
            }

            //On génère une couleur aléatoire hexadécimale pour chaque ressource, qu'on stocke en session pour ne pas que la couleur change à chaque rafraichissement de la page
            $session = $request->getSession();

            //Si la couleur liée à la ressource n'existe pas en session, on la génère
            if(!$session->has('color_'.$resStockage->getNom().$resStockage->getId())){
                $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
                $session->set('color_'.$resStockage->getNom().$resStockage->getId(), $color);
            }else{
                $color = $session->get('color_'.$resStockage->getNom().$resStockage->getId());
            }

            //On stocke les données de chaque ressource dans un tableau
            $dataSet[] = [
                'label' => $resStockage->getNom(),
                'borderColor' => $color,
                'data' => $data,
            ];

        }

        //On trie les dates pour les afficher dans l'ordre croissant (en prenant en compte le format de la date), il faut également traiter le cas où label est null
        usort($labels, function($a, $b) use ($format){
            $dateA = $this->convertDate($a, $format);
            $dateB = $this->convertDate($b, $format);

            if($dateA == $dateB){
                return 0;
            }

            return $dateA < $dateB ? -1 : 1;
        });


        //On crée le graphique
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => $labels,
            'datasets' => $dataSet,
        ]);

        //On échange de sens les labels pour les rendre plus lisibles
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Pourcentage',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Date',
                        ],
                ],
            ],
        ]);

        return $this->render('ressources/index.html.twig', [
            'resStockagesHome' => $resStockagesHome,
            'resStockagesWork' => $resStockageWork,
            'mesuresHome' => $mesureDeChaqueResHome,
            'mesuresWork' => $mesureDeChaqueResWork,
            'pourcentageHome' => $pourcentageHome,
            'pourcentageWork' => $pourcentageWork,
            'chart' => $chart,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Convertir une date en fonction de son format
     * @param $date
     * @param $format
     * @return \DateTime|false|string
     */
    private function convertDate($date, $format) : \DateTime|false|string {
        if($format == 'd/m/Y H:i:s') {
            return \DateTime::createFromFormat('d/m/Y H:i:s', $date)->format('Y-m-d');
        } elseif($format == 'W/Y') {
            // Convertir le format de semaine/année en une date
            list($week, $year) = explode('/', $date);
            $date = new \DateTime();
            $date->setISODate($year, $week);
            return $date->format('Y-m-d');
        } else {
            return \DateTime::createFromFormat('Y', $date)->format('Y-m-d');
        }
    }

}
