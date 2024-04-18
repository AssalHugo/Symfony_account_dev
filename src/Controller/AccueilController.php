<?php

namespace App\Controller;

use App\Entity\ResStockagesHome;
use App\Entity\ResStockageWork;
use App\Entity\StockagesMesuresHome;
use App\Entity\StockagesMesuresWork;
use App\Form\ContactSuppportType;
use App\Service\SenderMail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use App\Security\CustomAuthenticator;


class AccueilController extends AbstractController
{
    #[Route('', name: 'accueil')]
    public function index(EntityManagerInterface $entityManager, Security $security, Request $request, SenderMail $senderMail): Response
    {

        $user = $this->getUser();

        if ($user === null) {
            return $this->render('accueil/index.html.twig');
        }

        $resStockagesHome = $entityManager->getRepository(ResStockagesHome::class)->findBy(['user' => $user]);

        $mesureDeChaqueResHome = $entityManager->getRepository(StockagesMesuresHome::class)->findLatestMeasurementsByUser($user->getId());


        $employe = $user->getEmploye();

        //On récupère tous les groupes de l'utilisateur connecté
        $groupes = $employe->getGroupesSecondaires();
        //On ajoute le groupe principal à la liste des groupes secondaires
        $groupes[] = $employe->getGroupePrincipal();

        $mesureDeChaqueResWork = $entityManager->getRepository(StockagesMesuresWork::class)->findLatestMeasurementsByUser($groupes);

        $resStockagesWork = $entityManager->getRepository(ResStockageWork::class)->findByGroupes($groupes);


        //--------------------Partie contact support--------------
        $formContact = $this->createForm(ContactSuppportType::class);

        $formContact->add('envoyer', SubmitType::class, ['label' => 'Envoyer']);

        $formContact->handleRequest($request);

        if($formContact->isSubmitted() && $formContact->isValid()){

            //On récupère le corps du message 'corps'
            $message = $formContact->getData()['corps'];

            //On récupere tous les emails des admins
            $adminRepo = $entityManager->getRepository(User::class);
            $admins = $adminRepo->findByRole("ROLE_ADMIN");

            //On envoie un mail à chaque admin
            foreach ($admins as $admin) {

                try {
                    $senderMail->sendMail($user->getEmail(), $admin->getEmail(), 'Contact support', $message);
                }
                catch (TransportExceptionInterface $e) {
                    $session = $request->getSession();
                    $session->getFlashBag()->add('message', 'Le message n\'a pas pu être envoyé');
                    $session->set('statut', 'danger');
                    return $this->redirect($this->generateUrl('accueil'));
                }
            }

            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'Le message a bien été envoyé');
            $session->set('statut', 'success');

            return $this->redirectToRoute('accueil');
        }



        return $this->render('accueil/index.html.twig', [
            'resStockagesHome' => $resStockagesHome,
            'mesureDeChaqueResHome' => $mesureDeChaqueResHome,
            'mesureDeChaqueResWork' => $mesureDeChaqueResWork,
            'resStockagesWork' => $resStockagesWork,
            'formContact' => $formContact->createView(),
        ]);
    }
}
