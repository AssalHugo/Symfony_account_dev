<?php

namespace App\Controller;

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
    #[Route('/groupes/modifier', name: 'modifier')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        //On récupère l'employe qui est connecté
        $employe = $this->getUser()->getEmploye();

        $formGroupePrincipal = $this->createForm(GroupesPrincipalType::class,$employe);

        $groupesSecondairesEmploye = $employe->getGroupesSecondaires();

        $listeGroupes = $entityManager->getRepository(Groupes::class)->findAll();

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

            'formGroupePrincipal' => $formGroupePrincipal->createView(),
            'groupesSecondairesEmploye' => $groupesSecondairesEmploye,
            'formGroupesSecondaires' => $formGroupesSecondaires->createView(),
        ]);
    }

    #[Route('/user/groupes/removeGroupeSecondaire/{id}', name: 'removeGroupeSecondaire')]
    public function removeGroupeSecondaire(Request $request, EntityManagerInterface $entityManager, int $id): Response{

        
        $groupeRepo = $entityManager->getRepository(Groupes::class);
        $employe = $this->getUser()->getEmploye();

        $groupe = $groupeRepo->find($id);
        $employe->removeGroupesSecondaire($groupe);
        $entityManager->persist($employe);
        $entityManager->flush();

        $session = $request->getSession();
        $session->getFlashBag()->add('message', 'Le groupe a été supprimé');
        $session->set('statut', 'success');

        return $this->redirect($this->generateUrl('modifier'));
    }
}
