<?php

namespace App\DataFixtures;

use App\Entity\Departement;
use App\Entity\EtatsRequetes;
use App\Entity\ResStockagesHome;
use App\Entity\StockageMesuresHome;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Employe;
use App\Entity\User;
use App\Entity\Contrats;
use App\Entity\Status;
use App\Entity\Batiments;
use App\Entity\Localisations;
use App\Entity\Telephones;
use App\Entity\Groupes;

class HugoUserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $employe = new Employe();
        $employe->setNom("assal");
        $employe->setPrenom("hugo");
        $employe->setSyncReseda(true);
        $employe->setPagePro("page");
        $employe->setIdhal("sebastien-geiger");
        $employe->setOrcid("0000-0003-1412-9991");
        $employe->setMailSecondaire("hugo2@mail.com");
        $employe->setTelephoneSecondaire("0101010101");
        $employe->setAnneeNaissance(2024);
        $employe->setPhoto("user.png");
        $employe->setRedirectionMail(true);

        $employe2 = new Employe();
        $employe2->setNom("Jean");
        $employe2->setPrenom("Paul");
        $employe2->setSyncReseda(true);
        $employe2->setPagePro("page");
        $employe2->setIdhal("sebastien-geiger");
        $employe2->setOrcid("0000-0003-1412-9991");
        $employe2->setMailSecondaire("jean@jean");
        $employe2->setTelephoneSecondaire("0101010101");
        $employe2->setAnneeNaissance(2004);
        $employe2->setPhoto("user.png");
        $employe2->setRedirectionMail(true);

        $employe->setReferent($employe2);

        $user = new User();
        $user->setUsername("hassal");
        $user->setEmail("hugo@mail.com");
        $user->setPassword("hugo26**");
        $user->setEmploye($employe);

        $user2 = new User();
        $user2->setUsername("jean");
        $user2->setEmail("jean@mail.com");
        $user2->setPassword("jean26**");
        $user2->setEmploye($employe2);

        $status = new Status();
        $status->setType("Stagiaire");

        $status2 = new Status();
        $status2->setType("CDI");

        $status3 = new Status();
        $status3->setType("CDD");

        $status4 = new Status();
        $status4->setType("Chercheur");

        $status5 = new Status();
        $status5->setType("Doctorant");
        
        $contrat = new Contrats;
        $contrat->setDateDebut(new \DateTime("2015-09-31"));
        $contrat->setDateFin(new \DateTime("2016-09-31"));
        $contrat->setRemarque("remarque");
        $contrat->setQuotite(20);
        $contrat->setEmploye($employe);
        $contrat->setStatus($status);

        $contrat2 = new Contrats;
        $contrat2->setDateDebut(new \DateTime("2017-09-31"));
        $contrat2->setDateFin(new \DateTime("2018-09-31"));
        $contrat2->setRemarque("remarque 2");
        $contrat2->setQuotite(20);
        $contrat2->setEmploye($employe2);
        $contrat2->setStatus($status);

        $batiment = new Batiments();
        $batiment->setNom("4");

        $batiment2 = new Batiments();
        $batiment2->setNom("26");

        $batiment3 = new Batiments();
        $batiment3->setNom("48");

        $localisation = new Localisations();
        $localisation->setBureau("bureau 1");
        $localisation->setBatiment($batiment);

        $employe->addLocalisation($localisation);
        $employe2->addLocalisation($localisation);

        
        $telephone = new Telephones();
        $telephone->setNumero("0101010101");
        $telephone->setEmploye($employe);


        $groupe = new Groupes();
        $groupe->setNom("groupe 1");
        $groupe->setAcronyme("Grp1");
        $groupe->setResponsable($employe);


        $groupe2 = new Groupes();
        $groupe2->setNom("groupe 2");
        $groupe2->setAcronyme("Grp2");
        $groupe2->setResponsable($employe2);
        $groupe2->addAdjoint($employe);


        $departement = new Departement();
        $departement->setNom("Informatique");
        $departement->setAcronyme("Info");
        $departement->setResponsable($employe);

        $groupe2->setDepartement($departement);
        $groupe->setDepartement($departement);

        //On persiste les entités
        $manager->persist($user);
        $manager->persist($employe);
        $manager->persist($status);
        $manager->persist($contrat);
        $manager->persist($contrat2);
        $manager->persist($batiment);
        $manager->persist($batiment2);
        $manager->persist($batiment3);
        $manager->persist($localisation);
        $manager->persist($telephone);
        $manager->persist($groupe);
        $manager->persist($groupe2);
        $manager->persist($user2);
        $manager->persist($employe2);
        $manager->persist($departement);
        $manager->flush();

        $employe->setGroupePrincipal($groupe);
        $manager->persist($employe);
        $employe2->setGroupePrincipal($groupe2);
        $manager->persist($employe2);
        $manager->flush();


        //boucle qui crée 100 employés des groupes différents, des localisations différentes, des batiments différents, des départements différents, des contrats différents, des status différents, des téléphones différents
        for ($i=0; $i < 100; $i++) {
            $employe = new Employe();
            $employe->setNom("nom" . $i);
            $employe->setPrenom("prenom" . $i);
            $employe->setSyncReseda(true);
            $employe->setPagePro("page");
            $employe->setIdhal("sebastien-geiger");
            $employe->setOrcid("0000-0003-1412-9991");
            $employe->setMailSecondaire("mail secondaire" . $i);
            $employe->setTelephoneSecondaire("0101010101");
            $employe->setAnneeNaissance(2024);
            $employe->setPhoto("user.png");
            $employe->setRedirectionMail(true);


            $user2 = new User();
            $user2->setUsername("user" . $i);
            $user2->setEmail("mail" . $i);
            $user2->setPassword("password" . $i);
            $user2->setEmploye($employe);

            $contrat = new Contrats;
            $contrat->setDateDebut(new \DateTime("2015-09-31"));
            $contrat->setDateFin(new \DateTime("2016-09-31"));
            $contrat->setRemarque("remarque");
            $contrat->setQuotite(20);
            $contrat->setEmploye($employe);

            //On attribue un status différent aléatoirement
            if ($i % 2 == 0) {
                $contrat->setStatus($status);
            } else if ($i % 3 == 0) {
                $contrat->setStatus($status2);
            } else if ($i % 5 == 0) {
                $contrat->setStatus($status3);
            } else if ($i % 7 == 0) {
                $contrat->setStatus($status4);
            } else {
                $contrat->setStatus($status5);
            }

            //On crée une localisation différente aléatoirement
            $localisation = new Localisations();
            $localisation->setBureau("bureau" . $i);

            //On attribue un batiment différent aléatoirement
            if ($i % 2 == 0) {
                $localisation->setBatiment($batiment);
            } else if ($i % 3 == 0) {
                $localisation->setBatiment($batiment2);
            } else {
                $localisation->setBatiment($batiment3);
            }

            $employe->addLocalisation($localisation);

            $telephone = new Telephones();
            $telephone->setNumero("0101010101");
            $telephone->setEmploye($employe);

            $employe->setGroupePrincipal($groupe);

            //condition pour créer des groupes différents 1 fois sur 4
            if ($i % 4 == 0) {
                $groupe = new Groupes();
                $groupe->setNom("groupe" . $i);
                $groupe->setAcronyme("Grp" . $i);
                $groupe->setResponsable($employe);

                if ($i % 5 == 0) {
                    $departement = new Departement();
                    $departement->setNom("Departement" . $i);
                    $departement->setAcronyme("Dep" . $i);
                    $departement->setResponsable($employe);

                    $manager->persist($departement);
                }

                $groupe->setDepartement($departement);
                $manager->persist($groupe);

            }

            $manager->persist($employe);
            $manager->persist($user);
            $manager->persist($status);
            $manager->persist($contrat);
            $manager->persist($batiment);
            $manager->persist($localisation);
            $manager->persist($telephone);
        }

        //On initialise les différentes états de requêtes
        $etat1 = new EtatsRequetes();
        $etat1->setEtat("Demandé");

        $etat2 = new EtatsRequetes();
        $etat2->setEtat("Informations manquantes");

        $etat3 = new EtatsRequetes();
        $etat3->setEtat("Validé par admin");

        $etat4 = new EtatsRequetes();
        $etat4->setEtat("Refusé");

        $etat5 = new EtatsRequetes();
        $etat5->setEtat("Validé par RH");


        $manager->persist($etat1);
        $manager->persist($etat2);
        $manager->persist($etat3);
        $manager->persist($etat4);
        $manager->persist($etat5);


        $manager->flush();

        //On remplit le stockage home de fausses données
        $resStockageHome = new ResStockagesHome();
        $resStockageHome->setNom("home general");
        $resStockageHome->setPath("/home");
        $resStockageHome->setUser($user);

        for ($i=0; $i < 100; $i++) {
            $mesure = new StockageMesuresHome();
            $mesure->setDateMesure(new \DateTime("2021-09-31 + " . $i . " days"));
            $mesure->setValeurUse(rand(0, 10));
            $mesure->setValeurMax(10);
            $resStockageHome->addMesure($mesure);
            $manager->persist($mesure);
        }

        $resStockageHome2 = new ResStockagesHome();
        $resStockageHome2->setNom("home uc");
        $resStockageHome2->setUser($user);
        $resStockageHome2->setPath("/home" . $user->getUsername());

        for ($i=0; $i < 100; $i++) {
            $mesure = new StockageMesuresHome();
            //On sépare la date d'un jour pour chaque mesure
            $mesure->setDateMesure(new \DateTime("2021-09-31 + " . $i . " days"));
            $mesure->setValeurUse(rand(0, 20));
            $mesure->setValeurMax(20);
            $resStockageHome2->addMesure($mesure);
            $manager->persist($mesure);
        }

        $manager->persist($resStockageHome);
        $manager->persist($resStockageHome2);
        $manager->flush();
    }
}
