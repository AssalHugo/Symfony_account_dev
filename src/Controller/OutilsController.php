<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Entity\Employe;
use App\Entity\EtatsRequetes;
use App\Entity\Groupes;
use App\Entity\Requetes;
use App\Form\RequeteType;
use App\Form\TrombinoscopeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
        } //Sinon si le GET est vide
        else if (empty($request->query->all())) {
            //On récupère tous les utilisateurs
            $employes = $employeRepository->findAll();
        } //Sinon
        else {
            //On récupère les utilisateurs en fonction des filtres
            if ($request->query->get('departement') != null) {
                $departement = $request->query->get('departement');
            } else {
                $departement = "";
            }

            if ($request->query->get('groupe') != null) {
                $groupe = $request->query->get('groupe');
            } else {
                $groupe = "";
            }

            if ($request->query->get('statut') != null) {
                $statut = $request->query->get('statut');
            } else {
                $statut = "";
            }

            $employes = $employeRepository->findByFiltreId($departement, $groupe, $statut);
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
                if ($groupe != null && !in_array($groupe, $groupes)) {
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


    #[Route('/outils/formulaire/{indexRequete}', name: 'formulaireDemandeCompte', requirements: ['indexRequete' => '\d*'], defaults: ['indexRequete' => null])]
    public function formulaireDemandeCompte($indexRequete, Request $request, EntityManagerInterface $entityManager): Response
    {

        $requete = new Requetes();

        $formDemandeCompte = $this->createForm(RequeteType::class, $requete);

        $formDemandeCompte->add('valider', SubmitType::class, ['label' => 'Valider']);

        //Si l'index de la requete est null, on handle la requete de demande de compte
        if ($indexRequete == null) {
            $formDemandeCompte->handleRequest($request);
        }

        //Si le formulaire est soumis et valide et que le bouton valider est cliqué
        if ($formDemandeCompte->isSubmitted() && $formDemandeCompte->isValid() && $formDemandeCompte->get('valider')->isClicked()) {

            $employe = $this->getUser()->getEmploye();
            //On donne le référent qui est l'utilisateur connecté
            $requete->setReferent($employe);
            //On donne l'état de la requête qui est en attente
            $etatRequete = $entityManager->getRepository(EtatsRequetes::class)->findOneBy(['etat' => 'Demandé']);
            $requete->setEtatRequete($etatRequete);

            $entityManager->persist($requete);
            $entityManager->flush();

            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'La demande a bien été envoyée, aux RH pour validation.');
            $session->set('statut', 'success');

            return $this->redirectToRoute('formulaireDemandeCompte');
        }

        //On récupere toutes les requetes qui n'ont pas été validées par l'admin ou pas refusées de l'utilisateur connecté
        $requeteRepository = $entityManager->getRepository(Requetes::class);
        $etatRequeteRepo = $entityManager->getRepository(EtatsRequetes::class);

        $etatRequete = [];
        $etatRequete[] = $etatRequeteRepo->findOneBy(['etat' => 'Demandé']);
        $etatRequete[] = $etatRequeteRepo->findOneBy(['etat' => 'Information manquante']);
        $etatRequete[] = $etatRequeteRepo->findOneBy(['etat' => 'Validé par RH']);

        //On récupère les requetes coreespondantes
        $tabDemandes = $requeteRepository->findBy(['etat_requete' => $etatRequete, 'referent' => $this->getUser()->getEmploye()]);

        //on crée un formulaire pour chaque demande
        $tabFormDemande = [];
        foreach ($tabDemandes as $index => $demande) {
            $formDemande = $this->createForm(RequeteType::class, $demande);
            $formDemande->add('etat_requete', EntityType::class, [
                'class' => EtatsRequetes::class,
                'choice_label' => 'etat',
                'label' => 'Etat de la demande : ',
                'disabled' => true
            ]);
            $formDemande->add('id', HiddenType::class, ['mapped' => false, 'data' => $demande->getId()]);
            $formDemande->add('modifier', SubmitType::class, ['label' => 'Modifier', 'attr' => ['data-index' => $index]]);

            if ($index == $indexRequete) {
                $formDemande->handleRequest($request);

                if ($formDemande->isSubmitted() && $formDemande->isValid() && $formDemande->get('modifier')->isClicked() && $formDemande->get('id')->getData() == $demande->getId()) {
                    $entityManager->persist($demande);
                    $entityManager->flush();

                    $session = $request->getSession();
                    $session->getFlashBag()->add('message', 'La demande a bien été modifiée.');
                    $session->set('statut', 'success');

                    return $this->redirectToRoute('formulaireDemandeCompte');
                }
            }

            $tabFormDemande[] = $formDemande->createView();
        }

        return $this->render('outils/formulaireDemandeCompte.html.twig', [
            'formDemandeCompte' => $formDemandeCompte->createView(),
            'demandes' => $tabFormDemande
        ]);
    }
}
