<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Entity\Employe;
use App\Entity\Groupes;
use App\Form\TrombinoscopeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OutilsController extends AbstractController
{
    /**
     * Ce controlleur permet d'afficher le trombinoscope qui peut etre filtré par département, par groupe, par statut
     * @return Response
     */
    #[Route('/outils/trombinoscope', name: 'trombinoscope')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $employeRepository = $entityManager->getRepository(Employe::class);

        //On crée le formulaire de filtre
        $formFiltre = $this->createForm(TrombinoscopeType::class);

        $formFiltre->add('filtrer', SubmitType::class, ['label' => 'Filtrer']);

        $formFiltre->handleRequest($request);

        if ($formFiltre->isSubmitted() && $formFiltre->isValid()) {
            //On récupère les données du formulaire
            $data = $formFiltre->getData();
            //On récupère le département, le groupe et le statut
            $departement = $data['departement'];
            $groupe = $data['groupe'];
            $statut = $data['statut'];

            //On récupère les utilisateurs en fonction des filtres
            $employes = $employeRepository->findByFiltre($departement, $groupe, $statut);
        }
        else {
            //On récupère tous les utilisateurs
            $employes = $employeRepository->findAll();
        }

        //On récupère le nombres d'employés, de départements et de groupes au total dans la bd et le nombre affiché
        $nbEmployes = count($employeRepository->findAll());

        $departementRepository = $entityManager->getRepository(Departement::class);
        $nbDepartements = count($departementRepository->findAll());

        $groupeRepository = $entityManager->getRepository(Groupes::class);
        $nbGroupes = count($groupeRepository->findAll());

        $nbEmployesAffiches = count($employes);

        //On récupère le nombres de groupes et de départements affichés
        $nbGroupesAffiches = 0;
        $groupes = [];
        $nbDepartementsAffiches = 0;
        $departements = [];
        foreach ($employes as $employe) {
            $groupePrincipal = $employe->getGroupePrincipal();
            if ($groupePrincipal != null && !in_array($groupePrincipal, $groupes)) {
                $nbGroupesAffiches++;
                //On regarde si le département du groupe principal est déjà dans le tableau
                if ($groupePrincipal->getDepartement() != null && !in_array($groupePrincipal->getDepartement(), $departements)) {
                    $nbDepartementsAffiches++;
                }
            }
            $groupes[] = $groupePrincipal;
            $departements[] = $groupePrincipal->getDepartement();

            //Pour chaque groupes secondaires de l'employé, on vérifie si il est déjà dans le tableau
            foreach ($employe->getGroupesSecondaires() as $groupe) {
                if ($groupe != null && in_array($groupe, $groupes)) {
                    $nbGroupesAffiches++;
                    if ($groupe->getDepartement() != null && !in_array($groupe->getDepartement(), $departements)) {
                        $nbDepartementsAffiches++;
                    }
                }
                $groupes[] = $groupe;
                $departements[] = $groupe->getDepartement();
            }
        }


        return $this->render('outils/trombinoscope.html.twig', [
            'employes' => $employes,
            'formFiltre' => $formFiltre->createView(),
            'nbEmployes' => $nbEmployes,
            'nbDepartements' => $nbDepartements,
            'nbGroupes' => $nbGroupes,
            'nbEmployesAffiches' => $nbEmployesAffiches,
            'nbDepartementsAffiches' => $nbDepartementsAffiches,
            'nbGroupesAffiches' => $nbGroupesAffiches
        ]);
    }
}
