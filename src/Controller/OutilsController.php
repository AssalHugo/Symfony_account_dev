<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Entity\Employe;
use App\Entity\EtatsRequetes;
use App\Entity\Groupes;
use App\Entity\Requetes;
use App\Entity\User;
use App\Form\RedirectionMailType;
use App\Form\RequeteType;
use App\Form\TrombinoscopeType;
use App\Service\FlashBag;
use App\Service\SenderMail;
use App\Service\Trombinoscope;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class OutilsController extends AbstractController
{

    private PaginatorInterface $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Ce controlleur permet d'afficher le trombinoscope qui peut etre filtré par département, par groupe, par statut
     * @return Response
     */
    #[Route('/outils/trombinoscope', name: 'trombinoscope')]
    public function trombinoscope(Request $request, EntityManagerInterface $entityManager, Trombinoscope $trombinoscope, FlashBag $flashBag): Response
    {
        $employeRepository = $entityManager->getRepository(Employe::class);

        //On crée le formulaire de filtre
        $formFiltre = $this->createForm(TrombinoscopeType::class);

        $formFiltre->add('filtrer', SubmitType::class, ['label' => 'Filtrer']);

        $formFiltre->handleRequest($request);

        //On récupère la query en fonction des filtres
        $query = $trombinoscope->getQueryBuilder($formFiltre, $request, $employeRepository);

        //On ne récupere que les employés actifs (qui ont une date de début de contrat inférieure à la date du jour et une date de fin de contrat supérieure à la date du jour)
        $query
            ->andWhere('c.date_debut <= :date')
            ->andWhere('c.date_fin >= :date')
            ->setParameter('date', new \DateTime('now'));

        $page = $request->query->getInt('p', 1);

        //On récupère le nombres d'employés, de départements et de groupes au total dans la bd et le nombre affiché
        $nbEmployes = $employeRepository->countEmployesEntreDates();

        $departementRepository = $entityManager->getRepository(Departement::class);
        $nbDepartements = $departementRepository->countDepartements();

        $groupeRepository = $entityManager->getRepository(Groupes::class);
        $nbGroupes = $groupeRepository->countGroupes();

        $query2 = $query;
        $queryResult = $query2->getQuery()->getResult();
        $nbEmployesAffiches = count($queryResult);

        //On récupère le nombres de groupes et de départements affichés
        $nbGroupesAffiches = 0;
        $nbDepartementsAffiches = 0;
        $trombinoscope->setNbGroupesAffichesEtNbDepAffiches($queryResult, $nbGroupesAffiches, $nbDepartementsAffiches);

        //On pagine
        $employes = $this->paginator->paginate(
            $query->getQuery(),
            $page,
            30
        );

        //On traite si aucun employés
        if (count($employes) == 0) {
            $flashBag->flashBagDanger('Aucun employé ne correspond à votre recherche.', $request);
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

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/outils/supprimerFiltreSession', name: 'supprimerFiltreSession')]
    public function supprimerFiltreSession(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('nom');
        $session->remove('prenom');
        $session->remove('departement');
        $session->remove('groupe');
        $session->remove('statutEmploye');

        return $this->redirectToRoute('trombinoscope');
    }


    /**
     * @param $indexRequete
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param MailerInterface $mailer
     * @return Response
     */
    #[Route('/outils/formulaire/{indexRequete}', name: 'formulaireDemandeCompte', requirements: ['indexRequete' => '\d*'], defaults: ['indexRequete' => null])]
    public function formulaireDemandeCompte($indexRequete, Request $request, EntityManagerInterface $entityManager, SenderMail $senderMail): Response {

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
            //On donne la date de la requête à la seconde près
            $requete->setDateRequete(new \DateTime('now'));

            $entityManager->persist($requete);
            $entityManager->flush();

            //On envoie un mail aux RH et aux admins pour les informer de la demande
            $userRepo = $entityManager->getRepository(User::class);
            $rh = $userRepo->findByRole('ROLE_RH');
            $admin = $userRepo->findByRole('ROLE_ADMIN');

            //On merge les deux
            $recipients = array_merge($rh, $admin);

            foreach ($recipients as $recipient) {

                $text = "Demande de création de compte informatique : \n
                        L'utilisateur " . $employe->getNom() . " " . $employe->getPrenom() . " a demandé la création du compte informatique, suivant : \n
                        Nom : " . $requete->getNom() . "\n
                        Prénom : " . $requete->getPrenom() . "\n
                        Mail : " . $requete->getMail() . "\n
                        Téléphone : " . $requete->getTelephone() . "\n
                        Commentaire : " . $requete->getCommentaire() . "\n
                        Merci de bien vouloir valider ou refuser la demande.";

                try {
                    $senderMail->sendMail('mail@mail.com', $recipient->getEmail(), 'Demande de compte', $text);
                } catch (TransportExceptionInterface $e) {
                    $session = $request->getSession();
                    $session->getFlashBag()->add('message', 'Erreur lors de l\'envoi du mail.');
                    $session->set('statut', 'danger');
                    return $this->redirectToRoute('formulaireDemandeCompte');
                }
            }

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

        return $this->render('outils/formulaireDemandeCompte.html.twig', [
            'formDemandeCompte' => $formDemandeCompte->createView(),
            'demandes' => $tabDemandes
        ]);
    }

    /**
     * @param $id
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/outils/modifierDemandeCompteUser/{id}', name: 'modifierDemandeCompteUser')]
    public function modifierDemandeCompteUser($id, EntityManagerInterface $entityManager, Request $request, SenderMail $senderMail): Response
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

            return $this->redirectToRoute('formulaireDemandeCompte');
        }
        else if ($form->get('annuler')->isClicked()) {
            return $this->redirectToRoute('formulaireDemandeCompte');
        }

        return $this->render('rh/modifierDemandeCompte.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/outils/redirectionMail', name: 'redirectionMail')]
    public function redirectionMail(Request $request, EntityManagerInterface $entityManager): Response
    {

        //On récupère l'employe qui est connecté
        $employe = $this->getUser()->getEmploye();

        $formContacts = $this->createForm(RedirectionMailType::class, $employe);

        $formContacts->add('valider', SubmitType::class, ['label' => 'Valider']);

        $formContacts->handleRequest($request);

        if ($formContacts->isSubmitted() && $formContacts->isValid()) {

            $entityManager->persist($employe);
            $entityManager->flush();

            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'La redirection a bien été modifiée.');
            $session->set('statut', 'success');

            return $this->redirectToRoute('redirectionMail');
        }

        return $this->render('outils/redirectionMail.html.twig', [
            'formContacts' => $formContacts->createView(),
        ]);
    }
}
