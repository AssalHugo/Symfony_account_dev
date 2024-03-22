<?php

namespace App\Controller;

use App\Entity\Employe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\GroupesPrincipalType;
use App\Entity\Groupes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class GroupesController extends AbstractController
{
    /**
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/groupes/modifier', name: 'modifier')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        //On récupère l'employe qui est connecté
        $employe = $this->getUser()->getEmploye();

        //On crée le formulaire dans le controlleur
        $groupePrincipal = $employe->getGroupePrincipal();

        $groupesSecondairesEmploye = $employe->getGroupesSecondaires();

        //On récupere tous les groupes à l'exception des groupes que l'employé possède déjà
        $groupes = $entityManager->getRepository(Groupes::class)->findAll();

        $listeGroupes = [];

        foreach($groupes as $groupe){
            if(!$groupesSecondairesEmploye->contains($groupe) && $employe->getGroupePrincipal() != $groupe){
                $listeGroupes[] = $groupe;
            }
        }

        //On crée le formulaire dans le controlleur
        $formGroupesSecondaires = $this->createFormBuilder()->add('groupeSecondaire', ChoiceType::class, [
            'choices'  => $listeGroupes,
            'choice_value' => 'id',
            'choice_label' => function (?Groupes $Groupes): string {
                return $Groupes ? strtoupper($Groupes->getNom()) : '';
            },
        ], )->getForm();

        $formGroupesSecondaires->add('creer', SubmitType::class, ['label' => '+']);

        $formGroupesSecondaires->handleRequest($request);

        if($request->isMethod('POST') && $formGroupesSecondaires->isSubmitted() && $formGroupesSecondaires->isValid()){

            $groupeId = $formGroupesSecondaires->getData()['groupeSecondaire'];

            $groupe = $entityManager->getRepository(Groupes::class)->find($groupeId);

            $employe->addGroupesSecondaire($groupe);

            $entityManager->persist($employe);

            $entityManager->flush();

            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'Un nouveau groupe a été ajouté');
            $session->set('statut', 'success');

            return $this->redirect($this->generateUrl('modifier'));
        }

        return $this->render('groupes/modifier.html.twig', [

            'groupePrincipal' => $groupePrincipal,
            'groupesSecondairesEmploye' => $groupesSecondairesEmploye,
            'formGroupesSecondaires' => $formGroupesSecondaires->createView(),
        ]);
    }

    #[Route('/groupes/removeGroupeSecondaire/{id}', name: 'removeGroupeSecondaire')]
    public function removeGroupeSecondaire(Request $request, EntityManagerInterface $entityManager, int $id): Response{

        
        $groupeRepo = $entityManager->getRepository(Groupes::class);
        $employe = $this->getUser()->getEmploye();

        $groupe = $groupeRepo->find($id);
        $employe->removeGroupesSecondaire($groupe);
        $entityManager->persist($employe);
        $entityManager->flush();

        $session = $request->getSession();
        $session->getFlashBag()->add('message', "{$groupe->getNom()} a été supprimé");
        $session->set('statut', 'success');

        return $this->redirect($this->generateUrl('modifier'));
    }


    /**
     * Controleur qui permet d'afficher la liste des utilisateurs de chaque groupe appartenant à l'employé connecté
     */
    #[Route('/groupes/liste', name: 'liste')]
    public function liste(Request $request, EntityManagerInterface $entityManager): Response
    {
        $employe = $this->getUser()->getEmploye();

        $groupes = $employe->getGroupesSecondaires();

        //On ajoute le groupe principal à la liste des groupes secondaires
        $groupes[] = $employe->getGroupePrincipal();

        return $this->render('groupes/listeGroupes.html.twig', [
            'groupes' => $groupes,
        ]);
    }

    #[Route('/groupes/supprimerEmpGroupe/{idEmploye}/{idGroupe}', name: 'supprimerEmployeDuGroupe')]
    public function supprimerEmployeDuGroupe(Request $request, EntityManagerInterface $entityManager, int $idEmploye, int $idGroupe): Response
    {

        $employeRepo = $entityManager->getRepository(Employe::class);
        $employe = $employeRepo->find($idEmploye);

        $groupeRepo = $entityManager->getRepository(Groupes::class);
        $groupe = $groupeRepo->find($idGroupe);

        $employe->removeGroupesSecondaire($groupe);
        $entityManager->persist($employe);
        $entityManager->flush();

        $session = $request->getSession();
        $session->getFlashBag()->add('message', "L'employé a été supprimé du groupe");
        $session->set('statut', 'success');

        return $this->redirect($this->generateUrl('liste'));
    }


}
