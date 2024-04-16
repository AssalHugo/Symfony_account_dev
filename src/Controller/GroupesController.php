<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Entity\Localisations;
use App\Form\LocalisationType;
use App\Service\Remove;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Groupes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GroupesController extends AbstractController
{
    /**
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/groupes/modifier/{index}', name: 'modifier', requirements: ['index' => '\d*'], defaults: ['index' => null ])]
    public function index(Request $request, EntityManagerInterface $entityManager, $index): Response
    {

        //On récupère l'employe qui est connecté
        $employe = $this->getUser()->getEmploye();

        //On crée le formulaire dans le controlleur
        $groupePrincipal = $employe->getGroupePrincipal();

        $groupesSecondairesEmploye = $employe->getGroupesSecondaires();

        //On récupere tous les groupes à l'exception des groupes que l'employé possède déjà
        $groupesRepo = $entityManager->getRepository(Groupes::class);
        $groupes = $groupesRepo->findAll();

        //Ce tableau contiendra les groupes qui ne sont pas des groupes secondaires de l'employé et qui ne sont pas le groupe principal
        $listeGroupes = [];

        foreach($groupes as $groupe){
            if(!$groupesSecondairesEmploye->contains($groupe) && $employe->getGroupePrincipal() != $groupe){
                $listeGroupes[] = $groupe;
            }
        }

        $formsGroupesSecondaires = [];
        foreach ($groupesSecondairesEmploye as $i => $groupesSec){

            //On crée le formulaire dans le controlleur avec comme valeur par défaut le groupe secondaire de l'employé
            $form = $this->createFormBuilder(['groupeSecondaire' => $groupesSec])
                ->add('groupeSecondaire', ChoiceType::class, [
                    'choices'  => $groupes,
                    'label' => 'Nom du groupe : ',
                    'choice_value' => 'id',
                    'choice_label' => function (?Groupes $Groupes): string {
                        return $Groupes ? strtoupper($Groupes->getNom()) : '';
                    },
                ])
                ->getForm();

            $form->add('modifier', SubmitType::class, ['label' => 'Modifier', 'attr' => ['data-index' => $index]]);
            $form->handleRequest($request);

            //Si le formulaire est soumis et valide et que ce soit le bon formulaire
            if ($form->isSubmitted() && $form->isValid() && $form->get('modifier')->isClicked() && $i == $index) {
                // on supprime le groupe secondaire de l'employé et on le remplace par le nouveau groupe
                $groupe= $form->getData()['groupeSecondaire'];

                $employe->removeGroupesSecondaire($groupesSec);
                $employe->addGroupesSecondaire($groupe);

                $entityManager->persist($employe);
                $entityManager->flush();

                $session = $request->getSession();
                $session->getFlashBag()->add('message', 'Le groupe secondaire a bien été modifiée');
                $session->set('statut', 'success');

                return $this->redirect($this->generateUrl('modifier'));
            }

            $formsGroupesSecondaires[] = $form->createView();
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

        if($request->isMethod('POST') && $formGroupesSecondaires->isSubmitted() && $formGroupesSecondaires->isValid()) {

            $groupeID = $formGroupesSecondaires->getData()['groupeSecondaire'];

            $groupe = $groupesRepo->find($groupeID);

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
            'groupesSecondairesEmploye' => $formsGroupesSecondaires,
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
    public function supprimerEmployeDuGroupe(int $idEmploye, int $idGroupe, Remove $remove, Request $request, EntityManagerInterface $entityManager): Response {

        $groupeRepo = $entityManager->getRepository(Groupes::class);
        $groupe = $groupeRepo->find($idGroupe);

        //Si la personne connectée, est un admin, un RH, le responsable du groupe ou encore un adjoint du groupe
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_RH') || array_search($groupe, $this->getUser()->getEmploye()->getAdjointDe()->toArray()) !== false || $groupe->getResponsable() == $this->getUser()->getEmploye()) {
            $remove->supprimerEmployeDuGroupeSecond($idEmploye, $idGroupe, $request);
        }
        else {
            $session = $request->getSession();
            $session->getFlashBag()->add('message', "Vous n'avez pas les droits pour effectuer cette action");
            $session->set('statut', 'danger');
        }

        return $this->redirectToRoute('liste');
    }
}
