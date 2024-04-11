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
            if (count($mesures) == count($labels)) {

                foreach($mesures as $mesure){
                    $data[] = $mesure->getValeurUse() / $mesure->getValeurMax() * 100;
                }
            }
            else{
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
            }

            //On génère une couleur aléatoire hexadécimale pour chaque ressource, qu'on stocke en session pour ne pas que la couleur change à chaque rafraichissement de la page
            $color = $this->generateRandomColor($request, $resStockage);


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

        //On récupère les labels en fonction des serveurs
        $labelsMesures = $serveurMesuresRepo->findlabelEnFonctionDesServeurs($serveurs);

        $labels = [];
        //On met les labels dans le bon format
        foreach($labelsMesures as $label){
            $labels[] = $label['date_mesure']->format('d/m/Y H:i:s');
        }

        // On récupère les mesures de chaque serveur
        $mesureDeChaqueServeur = [];
        $mesureDeChaqueServeurRAM = [];
        foreach($serveurs as $serveur){
            //On récupère les mesures du serveur qui n'ont pas comme date de mesure une date inférieure à la date actuelle -30 jours
            $mesures = $serveurMesuresRepo->findMesuresEnFonctionDuServeur($serveur);
            if (count($mesures) == count($labels)) {

                $dataCpu = array_map(function($mesure) {
                    return $mesure->getCpu();
                }, $mesures);

                $dataRam = array_map(function($mesure) {
                    return $mesure->getRamUtilise();
                }, $mesures);

                $dataNbUsers = array_map(function($mesure) {
                    return $mesure->getNbUtilisateurs();
                }, $mesures);
            }//Sinon on cherche ou sont les trous dans les mesures et on les comble avec des valeurs nulles
            else {
                $dataCpu = [];
                $dataRam = [];
                $dataNbUsers = [];
                foreach($labels as $label){

                    $valeurCpu = null;
                    $valeurRam = null;
                    $valeurNbUsers = null;
                    foreach($mesures as $mesure){

                        // Si la date de la mesure est égale à la date du label, on récupère la valeur
                        if($label == $mesure->getDateMesure()->format('d/m/Y H:i:s')){
                            $valeurCpu = $mesure->getCpu();
                            $valeurRam = $mesure->getRamUtilise();
                            $valeurNbUsers = $mesure->getNbUtilisateurs();
                            break;
                        }
                    }
                    $dataCpu[] = $valeurCpu;
                    $dataRam[] = $valeurRam;
                    $dataNbUsers[] = $valeurNbUsers;
                }
            }


            // On génère une couleur aléatoire hexadécimale pour chaque serveurs, qu'on stocke en session pour ne pas que la couleur change à chaque rafraichissement de la page
            $color = $this->generateRandomColor($request, $serveur);

            $mesureDeChaqueServeur[] = [
                //On n'affiche pas le label sur le graphique, ni la couleur
                'label' => $serveur->getNom() . ' - CPU',
                'borderColor' => $color,
                'data' => $dataCpu,
                'yAxisID' => 'y-axis-1',
            ];
            $mesureDeChaqueServeurRAM[] = [
                'label' => $serveur->getNom() . ' - RAM',
                'borderColor' => $color,
                'data' => $dataRam,
                'yAxisID' => 'y-axis-1',
            ];
            $mesureDeChaqueServeur[] = [
                'label' => $serveur->getNom() . ' - Nb Users',
                'borderColor' => $color,
                'data' => $dataNbUsers,
                'yAxisID' => 'y-axis-2',
            ];
        }

        // On crée le graphique
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => $labels,
            'datasets' => $mesureDeChaqueServeur,
        ]);

            // On donne deux axes pour le graphique (un pour le nombre d'utilisateurs et l'autre pour le reste)
            $chart->setOptions([
            'scales' => [
                'y-axis-1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'RAM / CPU',
                    ],
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
                'y-axis-2' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'grid' => [
                        'display' => false,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Nb Users',
                    ],
                ],
            ],
        ]);

            //On crée un deuxième graphique pour la RAM
            $chartRAM = $chartBuilder->createChart(Chart::TYPE_LINE);

            $chartRAM->setData([
                'labels' => $labels,
                'datasets' => $mesureDeChaqueServeurRAM,
            ]);

            $chartRAM->setOptions([
                'scales' => [
                    'y-axis-1' => [
                        'type' => 'linear',
                        'display' => true,
                        'position' => 'left',
                        'title' => [
                            'display' => true,
                            'text' => 'RAM',
                        ],
                        'suggestedMin' => 0,
                        'suggestedMax' => 100,
                    ],
                ],
            ]);


        return $this->render('ressources/serveurs.html.twig', [
            'serveurs' => $serveurs,
            'chart' => $chart,
            'lastMesureDeChaqueServeur' => $lastMesureDeChaqueServeur,
            'chartRAM' => $chartRAM,
        ]);
    }

    /**
     * Generate random color
     * @param $request
     * @param $serveur
     * @return string
     */
    private function generateRandomColor($request, $serveur) : string
    {
        $session = $request->getSession();

        // Si la couleur liée à la ressource n'existe pas en session, on la génère
        if (!$session->has('color_' . $serveur->getNom() . $serveur->getId())) {
            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            $session->set('color_' . $serveur->getNom() . $serveur->getId(), $color);
        } else {
            $color = $session->get('color_' . $serveur->getNom() . $serveur->getId());
        }

        return $color;
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