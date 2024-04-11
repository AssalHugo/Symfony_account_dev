<?php

namespace App\Controller;

use App\Entity\Groupes;
use App\Entity\ResServeur;
use App\Entity\ResStockagesHome;
use App\Entity\ResStockageWork;
use App\Entity\ServeursMesures;
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
    #[Route('/ressources/stockage', name: 'stockage')]
    public function stockage(EntityManagerInterface $em, ChartBuilderInterface $chartBuilder, Request $request): Response {


        //On récupère les ressources de l'utilisateur connecté
        $user = $this->getUser();

        $resStockagesHomeRepository = $em->getRepository(ResStockagesHome::class);
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
        //On récupère tous les groupes de l'utilisateur connecté
        $employe = $user->getEmploye();

        $groupes = $employe->getGroupesSecondaires();

        //On ajoute le groupe principal à la liste des groupes secondaires
        $groupes[] = $employe->getGroupePrincipal();

        $resStockagesWorkRepo = $em->getRepository(ResStockageWork::class);

        //On récupère les dernières mesures de chaque ressource Work
        $mesureDeChaqueResWork = $em->getRepository(StockagesMesuresWork::class)->findLatestMeasurementsByUser($groupes);

        //On récupère la dernière mesure de chaque ressource Work
        $pourcentageWork = [];
        foreach($mesureDeChaqueResWork as $mesureResWork){

            //On calcule le pourcentage deux chiffres après la virgule entre la valeur actuelle et la valeur max
            $pourcentageWork[] = round(($mesureResWork->getValeurUse() / $mesureResWork->getValeurMax()) * 100, 2);
        }



        //On récupère les ressources de l'utilisateur connecté
        $resStockageWork = $resStockagesWorkRepo->findByGroupes($groupes);
        $resStockagesHome = $resStockagesHomeRepository->findBy(['user' => $user]);
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

        //Le format change en fonction de la période choisie
        if($periode == '30 days'){
            $format = 'd/m/Y H:i:s';
        }else if($periode == '1 year'){
            $format = 'W/Y';
        }
        else {
            $format = 'Y';
        }

        //On récupère d'abord tous les labels
        $labels = [];
        foreach($resStockages as $resStockage){
            //On récupère les mesures de la ressource qui ont la meme période que celle choisie
            $mesures = $this->getMesures($resStockage, $periode);

            foreach($mesures as $mesure){
                $label = $mesure->getDateMesure()->format($format);
                if(!in_array($label, $labels)) {
                    $labels[] = $label;
                }
            }
        }

        //On trie les labels par ordre croissant
        usort($labels, function($a, $b) use ($format){
            $dateA = $this->convertDate($a, $format);
            $dateB = $this->convertDate($b, $format);

            if($dateA == $dateB){
                return 0;
            }

            return $dateA < $dateB ? -1 : 1;
        });

        //Maintenant pour chaque ressource on regarde si elle a une mesure à la date du label et on récupère la valeur
        foreach($resStockages as $resStockage){
            //On récupère les mesures de la ressource qui ont la meme période que celle choisie
            $mesures = $this->getMesures($resStockage, $periode);

            $data = [];
            foreach($labels as $label){
                $valeur = null;
                foreach($mesures as $mesure){
                    //Si la date de la mesure est égale à la date du label, on récupère la valeur
                    if($label == $mesure->getDateMesure()->format($format)){
                        $valeur = $mesure->getValeurUse() / $mesure->getValeurMax() * 100;
                        break;
                    }
                }
                $data[] = $valeur;
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

        //On échange de sens les labels pour les rendre plus lisibles
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'min' => 0,
                    'suggestedMax' => 100,
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

        return $this->render('ressources/stockage.html.twig', [
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

    #[Route('/ressources/serveurs', name: 'serveurs')]
    public function serveurs(EntityManagerInterface $em, ChartBuilderInterface $chartBuilder, Request $request): Response {

        //On récupère les serveurs de l'utilisateur connecté
        $user = $this->getUser();

        $employe = $user->getEmploye();

        $groupes = $employe->getGroupesSecondaires();

        //On ajoute le groupe principal à la liste des groupes secondaires
        $groupes[] = $employe->getGroupePrincipal();

        $serveurMesuresRepo = $em->getRepository(ServeursMesures::class);

        //On récupère les dernières mesures de chaque serveur
        $lastMesureDeChaqueServeur = $serveurMesuresRepo->findLatestMeasurementsByUser($groupes);

        //On récupère les serveurs de l'utilisateur connecté
        $serveurs = $em->getRepository(ResServeur::class)->findByGroupes($groupes);

        //On récupère d'abord tous les labels
        $labels = [];
        foreach($serveurs as $serveur){
            //On récupère les mesures de la ressource qui ont la meme période que celle choisie
            $mesures = $serveur->getMesures()->filter(function($mesure){
                $dateMesure = $mesure->getDateMesure();
                $dateActuelle = new \DateTime();

                $dateActuelle->modify('-30 days');

                return $dateMesure >= $dateActuelle;
            });

            foreach($mesures as $mesure){
                $label = $mesure->getDateMesure()->format('d/m/Y H:i:s');
                if(!in_array($label, $labels)) {
                    $labels[] = $label;
                }
            }
        }

        //On trie les labels par ordre croissant
        usort($labels, function($a, $b){
            $dateA = \DateTime::createFromFormat('d/m/Y H:i:s', $a);
            $dateB = \DateTime::createFromFormat('d/m/Y H:i:s', $b);

            if($dateA == $dateB){
                return 0;
            }

            return $dateA < $dateB ? -1 : 1;
        });

        //On récupère les mesures de chaque serveur
        $mesureDeChaqueServeur = [];
        foreach($serveurs as $serveur){
            $mesures = $serveur->getMesures()->filter(function($mesure){
                $dateMesure = $mesure->getDateMesure();
                $dateActuelle = new \DateTime();

                $dateActuelle->modify('-30 days');

                return $dateMesure >= $dateActuelle;
            });

            $data = [];
            foreach($labels as $label){
                $valeur = null;
                foreach($mesures as $mesure){
                    //Si la date de la mesure est égale à la date du label, on récupère la valeur
                    if($label == $mesure->getDateMesure()->format('d/m/Y H:i:s')){
                        $valeur = $mesure->getCpu();
                        break;
                    }
                }
                $data[] = $valeur;
            }

            //On génère une couleur aléatoire hexadécimale pour chaque serveurs, qu'on stocke en session pour ne pas que la couleur change à chaque rafraichissement de la page
            $session = $request->getSession();

            //Si la couleur liée à la ressource n'existe pas en session, on la génère
            if(!$session->has('color_'.$serveur->getNom().$serveur->getId())){
                $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
                $session->set('color_'.$serveur->getNom().$serveur->getId(), $color);
            }else{
                $color = $session->get('color_'.$serveur->getNom().$serveur->getId());
            }

            $mesureDeChaqueServeur[] = [
                'label' => $serveur->getNom(),
                'borderColor' => $color,
                'data' => $data,
            ];
        }

        //On crée le graphique
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => $labels,
            'datasets' => $mesureDeChaqueServeur,
        ]);

        //On donne deux axes pour le graphique (un pour le nombre d'utilisateurs et l'autre pour le reste)
        $chart->setOptions([
            'scales' => [
                'yAxes' => [
                    [
                        'type' => 'linear',
                        'display' => true,
                        'position' => 'left',
                        'id' => 'y-axis-1',
                        'labels' => [
                            'show' => true,
                            'text' => 'CPU',
                        ],
                    ],
                    [
                        'type' => 'linear',
                        'display' => true,
                        'position' => 'right',
                        'id' => 'y-axis-2',
                        'gridLines' => [
                            'drawOnChartArea' => false,
                        ],
                        'labels' => [
                            'show' => true,
                            'text' => 'Nombre d\'utilisateurs',
                        ],
                    ],
                ],
            ],
        ]);


        return $this->render('ressources/serveurs.html.twig', [
            'serveurs' => $serveurs,
            'chart' => $chart,
            'lastMesureDeChaqueServeur' => $lastMesureDeChaqueServeur,
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
            return \DateTime::createFromFormat('d/m/Y H:i:s', $date);
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

    /**
     * récupère les mesures de la ressource qui ont la meme période que celle choisie
     */
    private function getMesures($resStockage, $periode) {
        return $resStockage->getMesures()->filter(function($mesure) use ($periode, &$i){

            $dateMesure = $mesure->getDateMesure();
            $dateActuelle = new \DateTime();

            if($periode == '30 days'){
                $dateActuelle->modify('-30 days');
            }elseif($periode == '1 year'){
                $dateActuelle->modify('-1 year');
            }else{
                $dateActuelle->modify('-5 years');
            }

            return $dateMesure >= $dateActuelle && $mesure->getPeriode()->getType() == $periode;
        });
    }

}
