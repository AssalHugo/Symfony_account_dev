<?php

namespace App\DataFixtures;

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

        $user = new User();
        $user->setUsername("hassal");
        $user->setEmail("hugo@mail.com");
        $user->setPassword("hugo26**");
        $user->setEmploye($employe);

        $status = new Status();
        $status->setType("Stagiaire");
        
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
        $contrat2->setEmploye($employe);
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

        
        $telephone = new Telephones();
        $telephone->setNumero("0101010101");
        $telephone->setEmploye($employe);


        $groupe = new Groupes();
        $groupe->setNom("groupe 1");
        $groupe->setAcronyme("Grp1");
        $groupe->setStatut("statut");
        

        $groupe->setResponsable($employe);
        
        $manager->persist($user);
        $manager->persist($employe);
        $manager->persist($groupe);
        
        
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
        $manager->flush();

        $employe->setGroupePrincipal($groupe);
        $manager->persist($employe);
        $manager->flush();
    }
}
