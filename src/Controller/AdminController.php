<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Entity\EtatsRequetes;
use App\Entity\Requetes;
use App\Entity\Telephones;
use App\Entity\User;
use App\Form\ChangerMDPType;
use App\Form\ChangerRoleType;
use App\Form\RequeteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/listeDemandesComptes', name: 'listeDemandesComptesAdmin')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        //On récupère les demandes de comptes ou le statut est 'Demandé'
        $requetesRepo = $entityManager->getRepository(Requetes::class);

        //On trie les demandes par date de demande ASC ne peut pas etre utilisé car c'est une date, il faut utiliser orderBy
        $demandesComptes = $requetesRepo->findOrderByDate();

        return $this->render('admin/demandesCompte.html.twig', [
            'demandes' => $demandesComptes,
        ]);
    }

    #[Route('/admin/verificationDemandeCompte/{id}', name: 'verificationDemandeCompteAdmin')]
    public function verificationDemandeCompte($id, EntityManagerInterface $entityManager, Request $request): Response
    {
        //On Réccupère la demande de compte
        $requetesRepo = $entityManager->getRepository(Requetes::class);

        $demandeCompte = $requetesRepo->find($id);

        //On récupère les employes qui ont le même nom et prénom que la demande de compte
        $employeRepo = $entityManager->getRepository(Employe::class);

        $employes = $employeRepo->findBy(['nom' => $demandeCompte->getNom(), 'prenom' => $demandeCompte->getPrenom()]);

        //On crée un formulaire pour séléctionner un des employés pour indiquer que c'est bien la personne dont on veut créer le compte
        $form = $this->createFormBuilder()
            ->add('employe', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => function ($employe) {
                    return $employe->getNom() . ' ' . $employe->getPrenom() . ' - ' . $employe->getContrats()[count($employe->getContrats()) - 1]->getDateDebut()->format('d/m/Y') . ' - ' . $employe->getContrats()[count($employe->getContrats()) - 1]->getDateFin()->format('d/m/Y');
                },
                'label' => 'La demande de compte correspond à cet employé :',
            ])
            ->add('valider', SubmitType::class, ['label' => 'Valider'])
            ->add('annuler', SubmitType::class, ['label' => 'Annuler', 'attr' => ['class' => 'btn-secondary']])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('valider')->isClicked()) {
            //On récupère les données du formulaire afin de modifier l'employe séléctionné avec les informations de la demande de compte
            $data = $form->getData();

            $employe = $data['employe'];
            $employe->setNom($demandeCompte->getNom());
            $employe->setPrenom($demandeCompte->getPrenom());
            $employe->setGroupePrincipal($demandeCompte->getGroupePrincipal());
            $employe->addLocalisation($demandeCompte->getLocalisation());
            $employe->addContrat($demandeCompte->getContrat());
            $employe->setSyncReseda(false);
            $employe->setPhoto("https://upload.wikimedia.org/wikipedia/commons/9/99/Sample_User_Icon.png");
            $employe->setRedirectionMail(true);

            $entityManager->persist($employe);
            $entityManager->flush();

            //On récupere l'objet EtatsRequetes avec l'état 'Validé par admin'
            $etatRequeteRepo = $entityManager->getRepository(EtatsRequetes::class);

            $etatRequete = $etatRequeteRepo->findOneBy(['etat' => 'Validé par admin']);

            //On change le statut de la demande de compte
            $demandeCompte->setEtatRequete($etatRequete);

            $entityManager->persist($demandeCompte);
            $entityManager->flush();

            //On crée un message flash pour informer l'utilisateur que la demande a bien été vérifiée
            $session = $request->getSession();
            $session->getFlashBag()->add('message', "L'employé a bien été mis à jour, avec les informations de la demande de compte.");
            $session->set('statut', 'success');

            return $this->redirectToRoute('listeDemandesComptesAdmin');
        }
        else if ($form->get('annuler')->isClicked()) {
            return $this->redirectToRoute('listeDemandesComptesAdmin');
        }

        return $this->render('admin/verificationDemandeCompte.html.twig', [
            'form' => $form->createView(),
            'employes' => $employes,
            'demande' => $demandeCompte,
        ]);

    }

    #[Route('/admin/validerDemandeCompte/{id}', name: 'validerDemandeCompteAdmin')]
    public function validerDemandeCompte($id, EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer): Response
    {
        //On récupère la demande de compte
        $requetesRepo = $entityManager->getRepository(Requetes::class);

        $demandeCompte = $requetesRepo->find($id);


        //On crée un nouvel utilisateur
        $user = new User();
        //On set le nom d'utilisateur de l'utilisateur la premiere lettre du prenom suivie du nom avec un max de 8 caracteres
        $username = substr($demandeCompte->getPrenom(), 0, 1) . $demandeCompte->getNom();
        $username = substr($username, 0, 8);

        //On vérifie si le nom d'utilisateur n'est pas déjà utilisé dans la base de données
        $userRepo = $entityManager->getRepository(User::class);
        $userMemeNom = $userRepo->findBy(['username' => $username]);
        //Si la taille de la liste est supérieure à 0, cela signifie que le nom d'utilisateur est déjà utilisé
        if (count($userMemeNom) > 0) {
            //On rajoute un chiffre à la fin du nom d'utilisateur
            $username = $username . count($userMemeNom);
        }
        $user->setUsername($username);

        //On set le mot de passe de l'utilisateur qui va etre généré automatiquement
        $mdp = self::randomPassword();

        //On hashe le mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $mdp);
        $user->setPassword($hashedPassword);

        //On set le role de l'utilisateur
        $user->setRoles(['ROLE_USER']);

        //On set l'email de l'utilisateur
        $user->setEmail($demandeCompte->getMail());

        //On crée un employé
        $employe = new Employe();
        $employe->setNom($demandeCompte->getNom());
        $employe->setPrenom($demandeCompte->getPrenom());
        $tel = new Telephones();
        $tel->setNumero($demandeCompte->getTelephone());
        $employe->addTelephone($tel);
        $employe->setGroupePrincipal($demandeCompte->getGroupePrincipal());
        $employe->addLocalisation($demandeCompte->getLocalisation());
        $employe->addContrat($demandeCompte->getContrat());
        $employe->setSyncReseda(false);
        $employe->setPhoto("https://upload.wikimedia.org/wikipedia/commons/9/99/Sample_User_Icon.png");
        $employe->setRedirectionMail(true);

        //On rajoute l'employe à l'utilisateur
        $user->setEmploye($employe);

        $entityManager->persist($employe);
        $entityManager->persist($user);
        $entityManager->persist($tel);
        $entityManager->flush();

        //On envoie un mail à l'utilisateur pour lui communiquer son mot de passe
        $message = "Bonjour " . $demandeCompte->getPrenom() . " " . $demandeCompte->getNom() . ",\n\n";
        $message .= "Votre demande de compte a bien été validée, avec les informations suivantes :\n";
        $message .= "Nom d'utilisateur : " . $username . "\n";
        $message .= "Mail : " . $demandeCompte->getMail() . "\n";
        $message .= "Mot de passe : " . $mdp . "\n\n";
        $message .= "Vous pouvez vous connecter à l'application avec votre adresse mail et ce mot de passe.\n\n";
        $message .= "Cordialement,\n\n";

        $email = (new Email())
            ->from('you@example.com')
            ->to($demandeCompte->getMail())
            ->subject('Votre demande de compte a été validée' . $demandeCompte->getPrenom() . ' ' . $demandeCompte->getNom())
            ->text($message);

        $mailer->send($email);


        //On récupere l'objet EtatsRequetes avec l'état 'Validé par admin'
        $etatRequeteRepo = $entityManager->getRepository(EtatsRequetes::class);

        $etatRequete = $etatRequeteRepo->findOneBy(['etat' => 'Validé par admin']);

        //On change le statut de la demande de compte
        $demandeCompte->setEtatRequete($etatRequete);

        $entityManager->persist($demandeCompte);
        $entityManager->flush();

        //On crée un message flash pour informer l'utilisateur que la demande a bien été validée
        $session = $request->getSession();
        $session->getFlashBag()->add('message', "L'utilisateur a bien été validé.");
        $session->set('statut', 'success');

        return $this->redirectToRoute('listeDemandesComptesAdmin');
    }

    static function randomPassword() : string{
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    #[Route('/admin/refuserDemandeCompte/{id}', name: 'refuserDemandeCompteAdmin')]
    public function refuserDemandeCompte($id, EntityManagerInterface $entityManager, Request $request): Response
    {
        //On récupère la demande de compte
        $requetesRepo = $entityManager->getRepository(Requetes::class);

        $demandeCompte = $requetesRepo->find($id);

        //On récupere l'objet EtatsRequetes avec l'état 'Refusé par admin'
        $etatRequeteRepo = $entityManager->getRepository(EtatsRequetes::class);

        $etatRequete = $etatRequeteRepo->findOneBy(['etat' => 'Refusé']);

        //On change le statut de la demande de compte
        $demandeCompte->setEtatRequete($etatRequete);

        $entityManager->persist($demandeCompte);
        $entityManager->flush();

        //On crée un message flash pour informer l'utilisateur que la demande a bien été refusée
        $session = $request->getSession();
        $session->getFlashBag()->add('message', "L'utilisateur a bien été refusé.");
        $session->set('statut', 'success');

        return $this->redirectToRoute('listeDemandesComptesAdmin');
    }

    #[Route('/rh/modifierDemandeCompteAdmin/{id}', name: 'modifierDemandeCompteAdmin')]
    public function modifierDemandeCompte($id, EntityManagerInterface $entityManager, Request $request): Response
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

            return $this->redirectToRoute('listeDemandesComptesAdmin');
        }
        else if ($form->get('annuler')->isClicked()) {
            return $this->redirectToRoute('listeDemandesComptesAdmin');
        }

        return $this->render('rh/modifierDemandeCompte.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Function qui va afficher le formulaire qui va permettre de changer le mot de passe d'un utilisateur
     */
    #[Route('/admin/changerMotDePasse', name: 'changerMotDePasseAdmin')]
    public function changerMotDePasse(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {

        //On crée un formulaire qui va permettre de changer le mot de passe d'un utilisateur
        $form = $this->createForm(ChangerMDPType::class);

        $form->add('submit', SubmitType::class, [
            'label' => 'Changer le mot de passe',
            'attr' => [
                'class' => 'btn btn-primary'
            ]
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //On récupère les données du formulaire
            $data = $form->getData();

            //On récupère l'utilisateur
            $user = $data['user'];

            //On change le mot de passe de l'utilisateur
            $mdp = $data['password'];
            //On hash le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $mdp);

            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            //On crée un message flash pour informer l'utilisateur que le mot de passe a bien été changé
            $session = $request->getSession();
            $session->getFlashBag()->add('message', "Le mot de passe a bien été changé.");
            $session->set('statut', 'success');

            return $this->redirectToRoute('changerMotDePasseAdmin');
        }

        return $this->render('admin/changerMDP.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/changerRole', name: 'changerRoleAdmin')]
    public function changerRole(Request $request, EntityManagerInterface $entityManager): Response
    {
        //On créé un formulaire qui va permettre de changer le role d'un utilisateur
        $form = $this->createForm(ChangerRoleType::class);

        $form->add('submit', SubmitType::class, [
            'label' => 'Changer le role',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //On récupère les données du formulaire
            $data = $form->getData();

            //On récupère l'utilisateur
            $user = $data['user'];

            //On récupère le role
            $role = $data['role'];
            //On met le role dans un tableau car la fonction setRoles attend un tableau
            $role = [$role];

            //On change le role de l'utilisateur
            $user->setRoles($role);

            $entityManager->persist($user);
            $entityManager->flush();

            //On crée un message flash pour informer l'utilisateur que le role a bien été changé
            $session = $request->getSession();
            $session->getFlashBag()->add('message', "Le role a bien été changé.");
            $session->set('statut', 'success');

            return $this->redirectToRoute('changerRoleAdmin');
        }

        return $this->render('admin/changerRole.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
