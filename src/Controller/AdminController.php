<?php

namespace App\Controller;

use App\Entity\EtatsRequetes;
use App\Entity\Requetes;
use App\Entity\User;
use App\Form\ChangerMDPType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/listeDemandesComptes', name: 'listeDemandesComptesAdmin')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        //On récupère les demandes de comptes ou le statut est 'Demandé'
        $requetesRepo = $entityManager->getRepository(Requetes::class);

        $demandesComptes = $requetesRepo->findByEtatRequete('Validé par RH');

        return $this->render('admin/demandesCompte.html.twig', [
            'demandes' => $demandesComptes,
        ]);
    }

    #[Route('/admin/validerDemandeCompte/{id}', name: 'validerDemandeCompteAdmin')]
    public function validerDemandeCompte($id, EntityManagerInterface $entityManager, Request $request): Response
    {
        //On récupère la demande de compte
        $requetesRepo = $entityManager->getRepository(Requetes::class);

        $demandeCompte = $requetesRepo->find($id);

        //On récupere l'objet EtatsRequetes avec l'état 'Validé par admin'
        $etatRequeteRepo = $entityManager->getRepository(EtatsRequetes::class);

        $etatRequete = $etatRequeteRepo->findOneBy(['etat' => 'Validé par admin']);

        //On change le statut de la demande de compte
        $demandeCompte->setEtatRequete($etatRequete);

        $entityManager->persist($demandeCompte);
        $entityManager->flush();

        //On crée un nouvel utilisateur
        /*$user = new User();
        //On set le nom d'utilisateur de l'utilisateur la premiere lettre du prenom suivie du nom avec un max de 8 caracteres
        $username = substr($demandeCompte->getEmploye()->getPrenom(), 0, 1) . $demandeCompte->getEmploye()->getNom();
        $username = substr($username, 0, 8);
        $user->setUsername($username);*/


        //On crée un message flash pour informer l'utilisateur que la demande a bien été validée
        $session = $request->getSession();
        $session->getFlashBag()->add('message', "L'utilisateur a bien été validé.");
        $session->set('statut', 'success');

        return $this->redirectToRoute('listeDemandesComptesAdmin');
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
}
