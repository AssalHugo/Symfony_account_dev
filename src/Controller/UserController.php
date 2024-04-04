<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UploadImageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
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
    /**
     * Fonction qui permet d'afficher les informations de l'employé connecté
     * @param $indexLocalisation
     * @param $indexTelephone
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param MailerInterface $mailer
     * @return Response
     */
    #[Route('/user/mesInformations/{indexTelephone}/{indexLocalisation}', name: 'mesInfos', requirements: ['indexTelephone' => '\d*', 'indexLocalisation' => '\d*'], defaults: ['indexTelephone' => null, 'indexLocalisation' => null])]
    public function mesInformations($indexLocalisation, $indexTelephone, Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response {

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


        //------------Partie localisation--------------
        //Pour chaques localisation de l'employe on crée un formulaire pour les modifier
        $formsLocalisations = [];
        foreach ($localisations as $i => $localisation){

            $newLocalisation = new Localisations();
            $newLocalisation->setBureau($localisation->getBureau());
            $newLocalisation->setBatiment($localisation->getBatiment());

            $form = $this->createForm(LocalisationType::class, $newLocalisation);
            $form->add('modifier', SubmitType::class, ['label' => 'Modifier', 'attr' => ['data-index' => $indexLocalisation]]);
            $form->handleRequest($request);

            //Si le formulaire est soumis et valide et que ce soit le bon formulaire
            if($form->isSubmitted() && $form->isValid() && $i == $indexLocalisation && $indexLocalisation != null){
                // Mettez à jour l'adresse original avec les nouvelles informations
                $localisation->setBureau($newLocalisation->getBureau());
                $localisation->setBatiment($newLocalisation->getBatiment());

                $entityManager->persist($localisation);
                $entityManager->flush();

                $session = $request->getSession();
                $session->getFlashBag()->add('message', 'La localisation a bien été modifiée');
                $session->set('statut', 'success');

                return $this->redirect($this->generateUrl('mesInfos'));
            }

            //On crée un formulaire pour chaque localisation
            $formsLocalisations[] = $form->createView();
        }


        //Partie pour ajouter une nouvelle localisation
        $nouvellelocalisation = new Localisations();
        $formLocalisation = $this->createForm(LocalisationType::class,$nouvellelocalisation);

        $formLocalisation->add('creer', SubmitType::class, ['label' => '+']);

        $formLocalisation->handleRequest($request);

        if($formLocalisation->isSubmitted() && $formLocalisation->isValid() && $indexLocalisation == null){

            $employe->addLocalisation($nouvellelocalisation);

            $entityManager->persist($nouvellelocalisation);
            $entityManager->flush();

            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'Une nouvelle localisation a été ajouté');
            $session->set('statut', 'success');

            return $this->redirect($this->generateUrl('mesInfos'));
        }


        //-----Partie Telephone-----
        //Pour chaques numéros de téléphone de l'employe on crée un formulaire pour les modifier
        $formsTelephones = [];
        foreach ($telephones as $i => $telephone){
            // Créez une nouvelle instance de Telephones pour chaque formulaire
            $newTelephone = new Telephones();
            $newTelephone->setNumero($telephone->getNumero());

            $form = $this->createForm(TelephonesType::class, $newTelephone);
            $form->add('modifier', SubmitType::class, ['label' => 'Modifier', 'attr' => ['data-index' => $indexTelephone]]);
            $form->handleRequest($request);

            //Si le formulaire est soumis et valide et que ce soit le bon formulaire
            if($form->isSubmitted() && $form->isValid() && $i == $indexTelephone && $indexTelephone != null){
                // Mettez à jour le numéro de téléphone original avec les nouvelles informations
                $telephone->setNumero($newTelephone->getNumero());

                $entityManager->persist($telephone);
                $entityManager->flush();

                $session = $request->getSession();
                $session->getFlashBag()->add('message', 'Le téléphone a bien été modifié');
                $session->set('statut', 'success');

                return $this->redirect($this->generateUrl('mesInfos'));
            }

            //On crée un formulaire pour chaque téléphone
            $formsTelephones[] = $form->createView();
        }


        //Partie pour ajouter un nouveau téléphone
        $telephone = new Telephones();
        $formTelephone = $this->createForm(TelephonesType::class,$telephone);

        $formTelephone->add('creer', SubmitType::class, ['label' => '+']);

        $formTelephone->handleRequest($request);

        if($formTelephone->isSubmitted() && $formTelephone->isValid()){

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

        if($formContact->isSubmitted() && $formContact->isValid()){

            //On récupère le corps du message 'corps'
            $message = $formContact->getData()['corps'];

            //On récupere tous les emails des admins
            $adminRepo = $entityManager->getRepository(User::class);
            $admins = $adminRepo->findByRole("ROLE_ADMIN");

            //On envoie un mail à chaque admin
            foreach ($admins as $admin) {

                $email = (new Email())
                    ->from('you@example.com')
                    ->to($admin->getEmail())
                    ->subject('Contact support')
                    ->text($message);

                try {
                    //$mailer->send($email);
                } catch (TransportExceptionInterface $e) {
                    $session = $request->getSession();
                    $session->getFlashBag()->add('message', 'Le message n\'a pas pu être envoyé');
                    $session->set('statut', 'danger');
                    return $this->redirect($this->generateUrl('mesInfos'));
                }
            }

            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'Le message a bien été envoyé');
            $session->set('statut', 'success');

            return $this->redirectToRoute('mesInfos');
        }


        //-----------------Partie upload photo profil-----------------
        $formUploadPhoto = $this->createForm(UploadImageType::class, $employe);
        $formUploadPhoto->add('valider', SubmitType::class, ['label' => 'Valider']);

        $formUploadPhoto->handleRequest($request);

        if($formUploadPhoto->isSubmitted() && $formUploadPhoto->isValid()){


            $entityManager->persist($employe);
            $entityManager->flush();

            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'La photo de profil a bien été modifiée');
            $session->set('statut', 'success');

            return $this->redirectToRoute('mesInfos');
        }

        //On renvoie vers le template accompagné de certaines valeurs
        return $this->render('user/mesInfo.html.twig', [
            'user' => $user,
            'employe' => $employe,
            'contrat' => $contrat,
            'contrats' => $contrats,
            'localisations' => $localisations,
            'formsLocalisations' => $formsLocalisations,
            'formLocalisation' => $formLocalisation->createView(),
            'formsTelephones' => $formsTelephones,
            'formTelephone' => $formTelephone->createView(),
            'formContact' => $formContact->createView(),
            'telephones' => $telephones,
            'formPhoto' => $formUploadPhoto->createView(),
        ]);
    }

    function uuid($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Fonction qui permet de supprimer une localisation
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    #[Route('/user/mesInformations/removeLocalisation/{id}', name: 'removeLocalisation')]
    public function removeLocalisation(Request $request, EntityManagerInterface $entityManager, $id): Response{

        //On récupère la localisation à supprimer
        $locaRepo = $entityManager->getRepository(Localisations::class);
        $localisation = $locaRepo->find($id);
        //On la supprime
        $entityManager->remove($localisation);
        $entityManager->flush();

        $session = $request->getSession();
        $session->getFlashBag()->add('message', 'La localisation  a été supprimé');
        $session->set('statut', 'success');

        return $this->redirect($this->generateUrl('mesInfos'));
    }


    /**
     * Fonction qui permet de supprimer un téléphone
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param int $id
     * @return Response
     */
    #[Route('/user/mesInformations/removeTelephone/{id}', name: 'removeTelephone')]
    public function removeTelephone(Request $request, EntityManagerInterface $entityManager, int $id): Response{

        //On récupère le téléphone à supprimer
        $teleRepo = $entityManager->getRepository(Telephones::class);
        $telephone = $teleRepo->find($id);
        //On le supprime
        $entityManager->remove($telephone);
        $entityManager->flush();

        $session = $request->getSession();
        $session->getFlashBag()->add('message', 'Le téléphone a été supprimé');
        $session->set('statut', 'success');

        return $this->redirect($this->generateUrl('mesInfos'));
    }


    /**
     * Fonction qui permet de modifier les identifiants numériques de l'employé
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param HttpClientInterface $client
     * @return Response
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    #[Route('/user/identifiantsNumeriques', name: 'idNum')]
    public function identidiantsNumeriques(Request $request, EntityManagerInterface $entityManager, HttpClientInterface $client): Response{

        //On récupère l'employe qui est connecté
        $employe = $this->getUser()->getEmploye();


        //On crée un formulaire pour modifier les identifiants numériques
        $formIdentifiants = $this->createForm(IdentifiantsType::class, $employe);
        $formIdentifiants->add('valider', SubmitType::class, ['label' => 'Valider']);

        $formIdentifiants->handleRequest($request);

        if($formIdentifiants->isSubmitted() && $formIdentifiants->isValid()){

            $entityManager->persist($employe);
            $entityManager->flush();

            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'Les identifiants ont bien étés modifiés');
            $session->set('statut', 'success');

            return $this->redirect($this->generateUrl('idNum'));
        }


        //---------Partie test publications-----------
        $formTest = $this->createForm(TestPublicationType::class);
        $formTest->add('tester', SubmitType::class, ['label' => 'Tester']);

        $formTest->handleRequest($request);

        $content = "";

        if($formTest->isSubmitted() && $formTest->isValid()){

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

            if (isset($tabResponse["response"]["docs"]) && count($tabResponse["response"]["docs"]) > 0)
                $tabResponse = $tabResponse["response"]["docs"];
            else
                $tabResponse[0] = ["label_s" => "Aucune publication trouvée", "uri_s" => "https://api.archives-ouvertes.fr/search/?fq=(+{$q}+)" ];


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
    public function contactSecondaires(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer) : Response{

        //On récupère l'employe qui est connecté
        $employe = $this->getUser()->getEmploye();

        $formContacts = $this->createForm(ContactSecondairesType::class, $employe);

        $formContacts->add('valider', SubmitType::class, ['label' => 'Valider']);

        $formContacts->handleRequest($request);

        if($formContacts->isSubmitted() && $formContacts->isValid()){

            $entityManager->persist($employe);
            $entityManager->flush();

            //On envoie un mail sur son adresse mail secondaire
            $email = (new Email())
                ->from('mail@gmail.com')
                ->to($employe->getMailSecondaire())
                ->subject('Modification des contacts secondaires')
                ->text('Les contacts secondaires ont bien étés modifiés');

            //$mailer->send($email);

            $session = $request->getSession();
            $session->getFlashBag()->add('message', 'Les contacts secondaires ont bien étés modifiés, un mail vous a été envoyé sur votre adresse mail secondaire');
            $session->set('statut', 'success');

            return $this->redirect($this->generateUrl('contacts'));
        }

        return $this->render('user/ContatcSecondaires.html.twig', [

            'formContacts' => $formContacts,
        ]);
    }
}
