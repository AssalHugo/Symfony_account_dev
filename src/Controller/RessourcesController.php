<?php

namespace App\Controller;

use App\Entity\GroupesSys;
use App\Entity\ResStockagesHome;
use App\Entity\ResStockageWork;
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

        $resStockagesHome = $em->getRepository(ResStockagesHome::class)->findBy(['user' => $user]);

        //On récupere la derniere mesure de chaque ressource Home
        $mesureDeChaqueResHome = [];
        $pourcentageHome = [];
        foreach($resStockagesHome as $resStockage){
            $mesure = $resStockage->getMesures()->first();


            //On calcule le pourcentage deux chiffres après la virgule entre la valeur actuelle et la valeur max
            $pourcentageHome[] = round(($mesure->getValeurUse() / $mesure->getValeurMax()) * 100, 2);

            $mesureDeChaqueResHome[] = $mesure;
        }



        //Partie Work
        //On récupère les ressources de l'utilisateur connecté
        //On récupère tous les groupesSys de l'utilisateur connecté
        $groupeSys = $em->getRepository(GroupesSys::class)->findBy(['user' => $user]);

        //On récupère les ressources de chaque groupeSys
        $resStockageWork = $em->getRepository(ResStockageWork::class)->findBy(['groupeSys' => $groupeSys]);

        //On récupère la dernière mesure de chaque ressource Work
        $mesureDeChaqueResWork = [];
        $pourcentageWork = [];
        foreach($resStockageWork as $resStockage){
            $mesure = $resStockage->getMesures()->first();
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
            foreach($mesures as $mesure){
                $data[] = $mesure->getValeurUse();

                //On stocke les dates des mesures dans un tableau si elles ne sont pas déjà stockées
                if(!in_array($mesure->getDateMesure()->format($format), $labels)) {

                    $labels[] = $mesure->getDateMesure()->format($format);
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
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Valeur utilisée',
                    ],
                ],
                'x' => [
                    'reverse' => true,
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

}
