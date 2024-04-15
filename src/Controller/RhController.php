<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Entity\Employe;
use App\Entity\EtatsRequetes;
use App\Entity\Groupes;
use App\Entity\Requetes;
use App\Entity\User;
use App\Form\AjouterGroupeType;
use App\Form\DepartementType;
use App\Form\EmployeInformationsType;
use App\Form\RequeteType;
use App\Form\ResponsableType;
use App\Form\TrombinoscopeType;
use App\Service\DemandeCompte;
use App\Service\Remove;
use App\Service\SenderMail;
use App\Service\Trombinoscope;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

class RhController extends AbstractController
{
    /**
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/rh/listeDemandesComptes', name: 'listeDemandesComptes')]
    public function listeDemandesComptes(EntityManagerInterface $entityManager): Response
    {
        //On récupère les demandes de comptes ou le statut est 'Demandé'
        $requetesRepo = $entityManager->getRepository(Requetes::class);

        //On récupère les demandes de comptes ou le statut est 'Demandé' ainsi que les demandes de comptes ou le statut est 'Informations manquantes'
        $demandesComptesDemandes = $requetesRepo->findByEtatRequete('Demandé');
        $demandesComptesInfoManquantes = $requetesRepo->findByEtatRequete('Informations manquantes');

        $demandesComptes = array_merge($demandesComptesDemandes, $demandesComptesInfoManquantes);

        return $this->render('rh/demandesCompte.html.twig', [
            'demandes' => $demandesComptes,
        ]);
    }

    /**
     * @param $id
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param SenderMail $senderMail
     * @return Response
     */
    #[Route('/rh/validerDemandeCompte/{id}', name: 'validerDemandeCompte')]
    public function validerDemandeCompte($id, EntityManagerInterface $entityManager, Request $request, SenderMail $senderMail): Response
    {
        //On récupère la demande de compte
        $requetesRepo = $entityManager->getRepository(Requetes::class);

        $demandeCompte = $requetesRepo->find($id);

        //On récupere l'objet EtatsRequetes avec l'état 'Validé par admin'
        $etatRequeteRepo = $entityManager->getRepository(EtatsRequetes::class);

        $etatRequete = $etatRequeteRepo->findOneBy(['etat' => 'Validé par RH']);

        //On change le statut de la demande de compte
        $demandeCompte->setEtatRequete($etatRequete);

        $entityManager->persist($demandeCompte);
        $entityManager->flush();

        //On envoie un mail aux admins pour les informer de la demande de compte
        $userRepo = $entityManager->getRepository(User::class);
        $admins = $userRepo->findByRole('ROLE_ADMIN');

        //On envoie un mail à chaque admin
        foreach ($admins as $admin) {

            $message = "Bonjour, \n\n";
            $message .= "L'utilisateur " . $demandeCompte->getPrenom() . " " . $demandeCompte->getNom() . " a été validé par le service RH. \n";
            $message .= "Vous pouvez maintenant créer son compte. \n\n";
            $message .= "Cordialement, \n";

            try {
                $senderMail->sendMail('mail@mail.com', $admin->getEmail(), 'Validation de demande de compte numéro : ' . $demandeCompte->getDateRequete()->format('YmdHis'), $message);
            } catch (TransportExceptionInterface $e) {
                $session = $request->getSession();
                $session->getFlashBag()->add('message', 'Erreur lors de l\'envoi du mail.');
                $session->set('statut', 'danger');
                return $this->redirect($this->generateUrl('validerDemandeCompte'));
            }
        }


        //On crée un message flash pour informer l'utilisateur que la demande a bien été validée
        $session = $request->getSession();
        $session->getFlashBag()->add('message', "L'utilisateur a bien été validé.");
        $session->set('statut', 'success');

        return $this->redirectToRoute('listeDemandesComptes');
    }

    #[Route('/rh/refuserDemandeCompte/{id}', name: 'refuserDemandeCompte')]
    public function refuserDemandeCompte($id, Request $request, DemandeCompte $demandeCompte): Response
    {

        $demandeCompte->refuserDemandeCompte($id, $request);

        return $this->redirectToRoute('listeDemandesComptes');
    }

    #[Route('/rh/modifierDemandeCompte/{id}', name: 'modifierDemandeCompte')]
    public function modifierDemandeCompte($id, EntityManagerInterface $entityManager, Request $request, SenderMail $senderMail): Response
    {
        //On récupère la demande de compte
        $requetesRepo = $entityManager->getRepository(Requetes::class);

        $demandeCompte = $requetesRepo->find($id);

        //On crée un formulaire pour modifier la demande de compte
        $form = $this->createForm(RequeteType::class, $demandeCompte);

        $form->add('valider', SubmitType::class, ['label' => 'Valider']);
        $form->add('annuler', SubmitType::class, ['label' => 'Annuler', 'attr' => ['class' => 'btn-secondary']]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('valider')->isClicked()) {
            $entityManager->persist($demandeCompte);
            $entityManager->flush();

            //On crée un message flash pour informer l'utilisateur que la demande a bien été modifiée
            $session = $request->getSession();
            $session->getFlashBag()->add('message', "La demande a bien été modifiée.");
            $session->set('statut', 'success');

            //On envoie un mail aux RH et aux admins pour les informer de la modification de la demande
            $user = $this->getUser();
            $from = $user->getEmail();

            $rh = $entityManager->getRepository(User::class)->findByRole('ROLE_RH');
            $admin = $entityManager->getRepository(User::class)->findByRole('ROLE_ADMIN');

            $recipients = array_merge($rh, $admin);

            $to = "";
            foreach ($recipients as $recipient) {
                $to .= $recipient->getEmail() . ', ';
            }

            $senderMail->sendMail($from, $to, 'Modification de la demande de compte : ' . $demandeCompte->getId(), 'La demande de compte' . $demandeCompte->getNom() . ' ' . $demandeCompte->getPrenom() . ' a été modifiée, par ' . $user->getEmploye()->getNom() . ' ' . $user->getEmploye()->getPrenom() . '.');


            return $this->redirectToRoute('listeDemandesComptes');
        } else if ($form->get('annuler')->isClicked()) {
            return $this->redirectToRoute('listeDemandesComptes');
        }

        return $this->render('rh/modifierDemandeCompte.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/rh/listeDepartements/{index}', name: 'listeDepartements', requirements: ['index' => '\d*'], defaults: ['index' => null])]
    public function listeGroupesParDepartements(EntityManagerInterface $entityManager, Request $request, $index): Response
    {
        //On récupère les departements
        $departementRepo = $entityManager->getRepository(Departement::class);

        $departements = $departementRepo->findAll();

        $formsResponsableDep = [];
        $formGroupes = [];
        foreach ($departements as $i => $departement) {
            //On crée un formulaire pour modifier le responsable du département
            $form = $this->createForm(DepartementType::class, $departement);

            $form->add('modifier', SubmitType::class, [
                'label' => 'Modifier',
                'attr' => ['data-index' => $index],
            ]);

            if ($index == $i) {
                $form->handleRequest($request);
            }


            //Si le formulaire est soumis, valide, que le bouton modifier est cliqué et que ce n'est pas le formulaire d'ajout d'un département
            if ($form->isSubmitted() && $form->isValid() && $index == $i && $form->get('modifier')->isClicked()) {

                $entityManager->persist($departement);
                $entityManager->flush();

                //On crée un message flash pour informer l'utilisateur que le responsable a bien été modifié
                $session = $request->getSession();
                $session->getFlashBag()->add('message', "Le responsable du département a bien été modifié.");
                $session->set('statut', 'success');

                return $this->redirectToRoute('listeDepartements');
            }

            //On crée un formulaire pour ajouter un groupe au département
            $groupe = new Groupes();
            $formAjoutGroupe = $this->createForm(AjouterGroupeType::class, $groupe);

            $formAjoutGroupe->add('ajouter', SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => ['data-index' => $i],
            ]);

            if ($index == $i) {
                $formAjoutGroupe->handleRequest($request);
            }

            if ($formAjoutGroupe->isSubmitted() && $formAjoutGroupe->isValid() && $index == $i && $formAjoutGroupe->get('ajouter')->isClicked()) {
                $groupe->setDepartement($departement);

                $entityManager->persist($groupe);
                $entityManager->flush();

                //On crée un message flash pour informer l'utilisateur que le groupe a bien été ajouté
                $session = $request->getSession();
                $session->getFlashBag()->add('message', "Le groupe a bien été ajouté.");
                $session->set('statut', 'success');

                return $this->redirectToRoute('listeDepartements');
            }

            $formsResponsableDep[] = $form->createView();
            $formGroupes[] = $formAjoutGroupe->createView();
        }


        //-------Formulaire ajout d'un département-------
        $departement = new Departement();
        //On crée un autre formulaire car sinon le formulaire de modification du responsable du département est soumis
        $formAjoutDepartement = $this->createFormBuilder($departement)
            ->add('nom')
            ->add('acronyme')
            ->add('responsable', EntityType::class, [
                    'class' => Employe::class,
                    'choice_label' => function ($employe) {
                        return $employe->getPrenom() . ' ' . $employe->getNom();
                    },
                    'label' => 'Responsable :',
                ]
            );

        $formAjoutDepartement->add('ajouter', SubmitType::class, [
            'label' => 'Ajouter',
        ]);

        $formAjoutDepartement = $formAjoutDepartement->getForm();

        $formAjoutDepartement->handleRequest($request);

        if ($formAjoutDepartement->isSubmitted() && $formAjoutDepartement->isValid() && $formAjoutDepartement->get('ajouter')->isClicked()) {

            $entityManager->persist($departement);
            $entityManager->flush();

            //On crée un message flash pour informer l'utilisateur que le département a bien été ajouté
            $session = $request->getSession();
            $session->getFlashBag()->add('message', "Le département a bien été ajouté.");
            $session->set('statut', 'success');

            return $this->redirectToRoute('listeDepartements');
        }


        return $this->render('rh/listeDepartements.html.twig', [
            'departements' => $departements,
            'formsResponsableDep' => $formsResponsableDep,
            'formGroupes' => $formGroupes,
            'formAjoutDepartement' => $formAjoutDepartement->createView(),
        ]);
    }

    #[Route('/rh/removeDepartement/{id}', name: 'removeDepartement')]
    public function removeDepartement($id, EntityManagerInterface $entityManager, Request $request): Response
    {
        //On récupère le departement
        $departementRepo = $entityManager->getRepository(Departement::class);

        $departement = $departementRepo->find($id);

        //Si le departement contient des groupes
        if ($departement->getGroupes()->count() > 0) {
            //On crée un message flash pour informer l'utilisateur que le département ne peut pas être supprimé
            $session = $request->getSession();
            $session->getFlashBag()->add('message', "Le département ne peut pas être supprimé car il contient des groupes.");
            $session->set('statut', 'danger');
        } else {
            //On supprime le département
            $entityManager->remove($departement);
            $entityManager->flush();

            //On crée un message flash pour informer l'utilisateur que le département a bien été supprimé
            $session = $request->getSession();
            $session->getFlashBag()->add('message', "Le département a bien été supprimé.");
            $session->set('statut', 'success');
        }

        return $this->redirectToRoute('listeDepartements');
    }

    #[Route('/rh/removeGroupe/{id}', name: 'removeGroupe')]
    public function removeGroupe($id, EntityManagerInterface $entityManager, Request $request): Response
    {
        //On récupère le groupe
        $groupesRepo = $entityManager->getRepository(Groupes::class);

        $groupe = $groupesRepo->find($id);

        //Si le groupe contient des employés
        if ($groupe->getEmployesGrpPrincipaux()->count() > 0 || $groupe->getEmployeGrpSecondaires()->count() > 0) {
            //On crée un message flash pour informer l'utilisateur que le groupe ne peut pas être supprimé
            $session = $request->getSession();
            $session->getFlashBag()->add('message', "Le groupe ne peut pas être supprimé car il contient des employés.");
            $session->set('statut', 'danger');
        } else {
            //On supprime le groupe
            $entityManager->remove($groupe);
            $entityManager->flush();

            //On crée un message flash pour informer l'utilisateur que le groupe a bien été supprimé
            $session = $request->getSession();
            $session->getFlashBag()->add('message', "Le groupe a bien été supprimé.");
            $session->set('statut', 'success');
        }

        return $this->redirectToRoute('listeDepartements');
    }

    #[Route('/rh/listeGroupe/{id}', name: 'listeGroupe')]
    public function listeGroupe($id, EntityManagerInterface $entityManager, Request $request): Response
    {

        //On récupère le departement
        $groupesRepo = $entityManager->getRepository(Groupes::class);

        $groupe = $groupesRepo->find($id);

        //On crée un formulaire pour modifier le responsable du groupe
        $form = $this->createForm(ResponsableType::class, $groupe);

        $form->add('submit', SubmitType::class, [
            'label' => 'Valider',
        ]);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($groupe);
            $entityManager->flush();

            //On crée un message flash pour informer l'utilisateur que le responsable a bien été modifié
            $session = $request->getSession();
            $session->getFlashBag()->add('message', "Le responsable et/ou les adjoints ont bien étés modifiés.");
            $session->set('statut', 'success');

            return $this->redirectToRoute('listeGroupe', ['id' => $id]);
        }

        //Formulaire ajout d'un employé dans le groupe
        //On récupère les employés qui ne sont pas dans le groupe
        $employeRepo = $entityManager->getRepository(Employe::class);

        $employes = $employeRepo->findAll();

        $employesGroupe = $groupe->getEmployesGrpPrincipaux()->toArray();

        $employesNonGroupe = array_udiff($employes, $employesGroupe,
            function ($obj_a, $obj_b) {
                return $obj_a->getId() - $obj_b->getId();
            }
        );

        //On met dans le formulaire les nom des employés
        $formAjoutEmploye = $this->createFormBuilder()
            ->add('groupesSecondaires', EntityType::class, [
                'choices' => $employesNonGroupe,
                'class' => Employe::class,
                'choice_label' => function ($employe) {
                    return $employe->getPrenom() . ' ' . $employe->getNom();
                },
                'label' => 'Ajouter un employé :',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter',
            ])
            ->getForm();

        $formAjoutEmploye->handleRequest($request);

        if ($formAjoutEmploye->isSubmitted() && $formAjoutEmploye->isValid()) {
            $data = $formAjoutEmploye->getData();

            $employe = $data['groupesSecondaires'];

            $employe->addGroupesSecondaire($groupe);

            $entityManager->persist($employe);
            $entityManager->flush();

            //On crée un message flash pour informer l'utilisateur que l'employé a bien été ajouté
            $session = $request->getSession();
            $session->getFlashBag()->add('message', "L'employé a bien été ajouté.");
            $session->set('statut', 'success');

            return $this->redirectToRoute('listeGroupe', ['id' => $id]);
        }


        return $this->render('rh/listeGroupe.html.twig', [
            'groupe' => $groupe,
            'form' => $form->createView(),
            'formAjoutEmploye' => $formAjoutEmploye->createView(),
        ]);
    }

    #[Route('/rh/supprimerEmpGroupe/{idEmploye}/{idGroupe}', name: 'supprimerEmployeDuGroupeRH')]
    public function supprimerEmployeDuGroupe(int $idEmploye, int $idGroupe, Remove $remove, Request $request): Response
    {

        $remove->supprimerEmployeDuGroupeSecond($idEmploye, $idGroupe, $request);

        return $this->redirectToRoute('listeGroupe', ['id' => $idGroupe]);
    }

    #[Route('/rh/infoEmploye/{idEmploye}/{idGroupe}', name: 'infoEmploye')]
    public function infoEmploye($idEmploye, $idGroupe, EntityManagerInterface $entityManager, Request $request): Response
    {
        //On récupère l'employé
        $employeRepo = $entityManager->getRepository(Employe::class);

        $employe = $employeRepo->find($idEmploye);

        //On récupère les localisations de l'employé
        $originalLocalisations = new ArrayCollection();
        foreach ($employe->getLocalisation() as $localisation) {
            $originalLocalisations->add($localisation);
        }

        $originalGroupesSecondaires = new ArrayCollection();
        foreach ($employe->getGroupesSecondaires() as $groupe) {
            $originalGroupesSecondaires->add($groupe);
        }


        //On crée un formulaire pour modifier les informations de l'employé
        $form = $this->createForm(EmployeInformationsType::class, $employe);

        $form->add('submit', SubmitType::class, [
            'label' => 'Modifier',
        ]);
        $form->add('annuler', SubmitType::class, [
            'label' => 'Annuler',
            'attr' => ['class' => 'btn-secondary'],
        ]);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('submit')->isClicked()) {

            //On supprime les localisations qui ne sont plus dans le formulaire
            foreach ($originalLocalisations as $localisation) {
                if (false === $employe->getLocalisation()->contains($localisation)) {
                    $entityManager->remove($localisation);
                }
            }

            //On supprime les groupes secondaires qui ne sont plus dans le formulaire
            foreach ($originalGroupesSecondaires as $groupe) {
                if (false === $employe->getGroupesSecondaires()->contains($groupe)) {
                    $entityManager->remove($groupe);
                }
            }

            $entityManager->persist($employe);
            $entityManager->flush();

            //On crée un message flash pour informer l'utilisateur que les informations ont bien été modifiées
            $session = $request->getSession();
            $session->getFlashBag()->add('message', "Les informations ont bien été modifiées.");
            $session->set('statut', 'success');

            return $this->redirectToRoute('infoEmploye', ['idEmploye' => $idEmploye, 'idGroupe' => $idGroupe]);
        } else if ($form->get('annuler')->isClicked()) {
            return $this->redirectToRoute('listeGroupe', ['id' => $idGroupe]);
        }

        return $this->render('rh/infoEmploye.html.twig', [
            'employe' => $employe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/rh/trombinoscope', name: 'trombinoscopeRH')]
    public function trombinoscope(EntityManagerInterface $entityManager, Request $request, Trombinoscope $trombinoscope): Response
    {
        $employeRepository = $entityManager->getRepository(Employe::class);

        //On crée le formulaire de filtre
        $formFiltre = $this->createForm(TrombinoscopeType::class);

        $formFiltre->add('filtrer', SubmitType::class, ['label' => 'Filtrer']);

        $formFiltre->handleRequest($request);

        $employes = $trombinoscope->getQuery($formFiltre, $request, $employeRepository)->getQuery()->getResult();

        //On récupère le nombres d'employés, de départements et de groupes au total dans la bd et le nombre affiché
        $nbEmployes = $employeRepository->countEmployes();

        $departementRepository = $entityManager->getRepository(Departement::class);
        $nbDepartements = $departementRepository->countDepartements();

        $groupeRepository = $entityManager->getRepository(Groupes::class);
        $nbGroupes = $groupeRepository->countGroupes();

        $nbEmployesAffiches = count($employes);

        //On récupère le nombres de groupes et de départements affichés
        $nbGroupesAffiches = 0;
        $nbDepartementsAffiches = 0;
        $trombinoscope->setNbGroupesAffichesEtNbDepAffiches($employes, $nbGroupesAffiches, $nbDepartementsAffiches);


    }
}