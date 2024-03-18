<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Contrats;
use App\Entity\Localisations;
use App\Entity\Telephones;
use Symfony\Component\HttpFoundation\Request;
use App\Form\LocalisationType;
use App\Form\TelephonesType;
use App\Form\ContactSuppportType;
use App\Form\IdentifiantsType;
use App\Form\TestPublicationType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Form\ContactSecondairesType;

class UserController extends AbstractController
{
    #[Route('/user/mesInformations', name: 'mesInfos')]
    public function mesInformations(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response {

        //On récupère le User connecté
        $user = $this->getUser();

        //On récupère l'employe qui est connecté
        $employe = $user->getEmploye();

        //On récupere le dernier contrat de l'employé 
        $contrat = $entityManager->getRepository(Contrats::class)->findLastContrat($employe->getId());

        //On récupere tous les contrats de l'employe
        $contrats = $employe->getContrats();

        //On récupere les localisations de l'employe
        $localisations = $employe->getLocalisation();

        //On récupere les numéros de téléphones de l'employe
        $telephones = $employe->getTelephones();


        //Partie localisation
        $localisation = new Localisations();
        $formLocalisation = $this->createForm(LocalisationType::class,$localisation);

        $formLocalisation->add('creer', SubmitType::class, ['label' => '+']);

        $formLocalisation->handleRequest($request);

        if($request->isMethod('POST') && $formLocalisation->isSubmitted() && $formLocalisation->isValid()){


            $employe->addLocalisation($localisation);

            $entityManager->persist($localisation);

            $entityManager->flush();

            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'Une nouvelle localisation a été ajouté');
            $session->set('statut', 'success');

            return $this->redirect($this->generateUrl('mesInfos'));
        }

        //-----Partie Telephone-----
        $telephone = new Telephones();
        $formTelephone = $this->createForm(TelephonesType::class,$telephone);

        $formTelephone->add('creer', SubmitType::class, ['label' => '+']);

        $formTelephone->handleRequest($request);

        if($request->isMethod('POST') && $formTelephone->isSubmitted() && $formTelephone->isValid()){


            $employe->addTelephone($telephone);

            $entityManager->persist($telephone);

            $entityManager->flush();

            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'Un nouveau téléphone a été ajouté');
            $session->set('statut', 'success');

            return $this->redirect($this->generateUrl('mesInfos'));
        }


        //----Partie contact support----
        $formContact = $this->createForm(ContactSuppportType::class);

        $formContact->add('envoyer', SubmitType::class, ['label' => 'Envoyer']);

        $formContact->handleRequest($request);

        if($request->isMethod('POST') && $formContact->isSubmitted() && $formContact->isValid()){

            $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'Le message a bien été envoyé');
            $session->set('statut', 'success');

            return $this->redirect($this->generateUrl('mesInfos'));
        }
        

        //On renvoie vers le template accompagné de certaines valeurs
        return $this->render('user/mesInfo.html.twig', [
            'user' => $user,
            'employe' => $employe,
            'contrat' => $contrat,
            'contrats' => $contrats,
            'localisations' => $localisations,
            'formLocalisation' => $formLocalisation->createView(),
            'formTelephone' => $formTelephone->createView(),
            'formContact' => $formContact->createView(),
            'telephones' => $telephones,
        ]);
    }

    #[Route('/user/mesInformations/removeLocalisation/{id}', name: 'removeLocalisation')]
    public function removeLocalisation(Request $request, EntityManagerInterface $entityManager, int $id): Response{

        $locaRepo = $entityManager->getRepository(Localisations::class);
        $localisation = $locaRepo->find($id);
        $entityManager->remove($localisation);
        $entityManager->flush();

        $session = $request->getSession();
        $session->getFlashBag()->add('message', 'La localisation  a été supprimé');
        $session->set('statut', 'success');

        return $this->redirect($this->generateUrl('mesInfos'));
    }


    #[Route('/user/mesInformations/removeTelephone/{id}', name: 'removeTelephone')]
    public function removeTelephone(Request $request, EntityManagerInterface $entityManager, int $id): Response{

        $teleRepo = $entityManager->getRepository(Telephones::class);
        $telephone = $teleRepo->find($id);
        $entityManager->remove($telephone);
        $entityManager->flush();

        $session = $request->getSession();
        $session->getFlashBag()->add('message', 'Le téléphone a été supprimé');
        $session->set('statut', 'success');

        return $this->redirect($this->generateUrl('mesInfos'));
    }


    #[Route('/user/identifiantsNumeriques', name: 'idNum')]
    public function identidiantsNumeriques(Request $request, EntityManagerInterface $entityManager, HttpClientInterface $client): Response{

        //On récupère l'employe qui est connecté
        $employe = $this->getUser()->getEmploye();


        $formIdentifiants = $this->createForm(IdentifiantsType::class, $employe);

        $formIdentifiants->add('valider', SubmitType::class, ['label' => 'Valider']);

        $formIdentifiants->handleRequest($request);

        if($request->isMethod('POST') && $formIdentifiants->isSubmitted() && $formIdentifiants->isValid()){

            $entityManager->persist($employe);

            $entityManager->flush();

            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'Les identifiants ont bien étés modifiés');
            $session->set('statut', 'success');

            return $this->redirect($this->generateUrl('idNum'));
        }


        //Partie test publication
        $formTest = $this->createForm(TestPublicationType::class);

        $formTest->add('tester', SubmitType::class, ['label' => 'Tester']);

        $formTest->handleRequest($request);

        $content = "";

        if($request->isMethod('POST') && $formTest->isSubmitted() && $formTest->isValid()){

            $content = "<h6> Listes des publications : </h6>";

            $data = $formTest->getData();

            $q = "";

            if ($data["idhal"] && $data["orcid"] ){

                $idhal = $employe->getIdhal();
                $orcid = $employe->getOrcid();

                $q = "authIdHal_s:" . $idhal . "%20OR%20authOrcidIdExt_id:" . $orcid;
            }
            else if ($data["idhal"] && !$data["orcid"] ){

                $idhal = $employe->getIdhal();
                $q = "authIdHal_s:" . $idhal;
            }
            else if (!$data["idhal"] && $data["orcid"]){

                $orcid = $employe->getOrcid();

                $q = $orcid;
            }
            else {//Si aucune cases n'est cochés on affiche la page

                return $this->render('user/identifiantsNumeriques.html.twig', [

                    'formIdentifiants' => $formIdentifiants,
                    'formTest' => $formTest,
                    'content' => "",
                ]);
            }
        
            $response = $client->request(
                'GET',
                "https://api.archives-ouvertes.fr/search/?fq=(+{$q}+)"
            );;

            $tabResponse = $response->toArray();

            $tabResponse = $tabResponse["response"]["docs"];

            foreach ($tabResponse as $key){
                if (isset($key["label_s"]) && isset($key["uri_s"])){

                    $content .= "<a href='" . $key["uri_s"] . "' target='_BLANK'>" . $key["label_s"] . "</a> </br> </br>";
                }
            }
        }

        return $this->render('user/identifiantsNumeriques.html.twig', [

            'formIdentifiants' => $formIdentifiants,
            'formTest' => $formTest,
            'content' => $content,
        ]);
    }

    #[Route('/user/contactSecondaires', name: 'contacts')]
    public function contactSecondaires(Request $request, EntityManagerInterface $entityManager) : Response{

        //On récupère l'employe qui est connecté
        $employe = $this->getUser()->getEmploye();

        $formContacts = $this->createForm(ContactSecondairesType::class, $employe);

        $formContacts->add('valider', SubmitType::class, ['label' => 'Valider']);

        $formContacts->handleRequest($request);

        if($request->isMethod('POST') && $formContacts->isSubmitted() && $formContacts->isValid()){

            $entityManager->persist($employe);

            $entityManager->flush();

            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'Les contacts secondaires ont bien étés modifiés');
            $session->set('statut', 'success');

            return $this->redirect($this->generateUrl('contacts'));
        }

        return $this->render('user/ContatcSecondaires.html.twig', [

            'formContacts' => $formContacts,
        ]);
    }
    
    

}
