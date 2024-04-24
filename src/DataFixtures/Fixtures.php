<?php

namespace App\DataFixtures;

use App\Entity\Departement;
use App\Entity\EtatsRequetes;
use App\Entity\Periode;
use App\Entity\ResServeur;
use App\Entity\ResStockagesHome;
use App\Entity\ResStockageWork;
use App\Entity\ServeursMesures;
use App\Entity\StockagesMesuresHome;
use App\Entity\StockagesMesuresWork;
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

class Fixtures extends Fixture
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
        $employe->setPhoto("admin.webp");
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
        $employe2->setPhoto("RH.jpg");
        $employe2->setRedirectionMail(true);
        $employe2->setReferent($employe);

        $employe->setReferent($employe2);

        $manager->persist($employe);
        $manager->persist($employe2);



        $user = new User();
        $user->setUsername("hassal");
        $user->setEmail("hugo@mail.com");
        $user->setPassword('$2y$13$JFDUleuuWN0RDgAFPx3OuOuh6O5kA7QquP1unEIQpum1c2HGKadfW');
        $user->setEmploye($employe);
        $user->setRoles(["ROLE_ADMIN"]);

        $user2 = new User();
        $user2->setUsername("rh");
        $user2->setEmail("jean@mail.com");
        $user2->setPassword("$2y$13$/Wa1X517naPcySLdKFkpheAlonXy6fvgT.AJD7gF1jrt38KcLF1Re");
        $user2->setEmploye($employe2);
        $user2->setRoles(["ROLE_RH"]);

        $manager->persist($user2);
        $manager->persist($user);

        $employeResp = new Employe();
        $employeResp->setNom("Alain");
        $employeResp->setPrenom("Pierre");
        $employeResp->setSyncReseda(true);
        $employeResp->setPagePro("page");
        $employeResp->setIdhal("sebastien-geiger");
        $employeResp->setOrcid("0000-0003-1412-9991");
        $employeResp->setMailSecondaire("alain2@mail.com");
        $employeResp->setTelephoneSecondaire("0101010101");
        $employeResp->setAnneeNaissance(2024);
        $employeResp->setPhoto("responsable.png");
        $employeResp->setRedirectionMail(true);
        $employeResp->setReferent($employe);

        $userResp = new User();
        $userResp->setUsername("responsable");
        $userResp->setEmail("responsable@mail.com");
        $userResp->setPassword('$2y$13$JKmcrFw2cDOKDZHGiq7PCeqqJkqguMmDHDZC1soigVZWbE6gayLV2');
        $userResp->setEmploye($employeResp);

        $manager->persist($userResp);
        $manager->persist($employeResp);


        $employeUser = new Employe();
        $employeUser->setNom("Pierre");
        $employeUser->setPrenom("Georges");
        $employeUser->setSyncReseda(true);
        $employeUser->setPagePro("page");
        $employeUser->setIdhal("sebastien-geiger");
        $employeUser->setOrcid("0000-0003-1412-9991");
        $employeUser->setMailSecondaire("pierre2@mail.com");
        $employeUser->setTelephoneSecondaire("0101010101");
        $employeUser->setAnneeNaissance(2024);
        $employeUser->setPhoto("user.png");
        $employeUser->setRedirectionMail(true);
        $employeUser->setReferent($employe);

        $userUser = new User();
        $userUser->setUsername("user");
        $userUser->setEmail("user@mail.com");
        $userUser->setPassword('$2y$13$jay3l6JKPlW4sWrc4L5zv.fO.OooPsjbxXKQUppqXq9UZ3LEvcR/S');
        $userUser->setEmploye($employeUser);

        $manager->persist($employeUser);
        $manager->persist($userUser);

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

        $status5 = new Status();
        $status5->setType("Collaborateur");
        
        $contrat = new Contrats;
        $contrat->setDateDebut(new \DateTime("2015-09-31"));
        $contrat->setDateFin(new \DateTime("2016-09-31"));
        $contrat->setRemarque("remarque");
        $contrat->setQuotite(20);
        $contrat->setEmploye($employe);
        $contrat->setStatus($status);

        $contrat2 = new Contrats;
        $contrat2->setDateDebut(new \DateTime("2017-09-31"));
        $contrat2->setDateFin(new \DateTime("2028-09-31"));
        $contrat2->setRemarque("remarque 2");
        $contrat2->setQuotite(20);
        $contrat2->setEmploye($employe2);
        $contrat2->setStatus($status);

        $contrat3 = new Contrats;
        $contrat3->setDateDebut(new \DateTime("2017-09-31"));
        $contrat3->setDateFin(new \DateTime("2025-09-31"));
        $contrat3->setRemarque("remarque 2");
        $contrat3->setQuotite(20);
        $contrat3->setEmploye($employeResp);
        $contrat3->setStatus($status);

        $manager->persist($contrat3);

        $contrat4 = new Contrats;
        $contrat4->setDateDebut(new \DateTime("2017-09-31"));
        $contrat4->setDateFin(new \DateTime("2024-09-31"));
        $contrat4->setRemarque("remarque 2");
        $contrat4->setQuotite(20);
        $contrat4->setEmploye($employeUser);
        $contrat4->setStatus($status);

        $manager->persist($contrat4);

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
        $groupe->setResponsable($employeResp);
        $employe->addGroupesSecondaire($groupe);
        $employeUser->addGroupesSecondaire($groupe);


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

        $employe->setGroupePrincipal($groupe);
        $manager->persist($employe);
        $employe2->setGroupePrincipal($groupe2);
        $manager->persist($employe2);
        $employeResp->setGroupePrincipal($groupe);
        $manager->persist($employeResp);
        $employeUser->setGroupePrincipal($groupe2);
        $manager->persist($employeUser);


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
            $employe->setReferent($employe);


            $user2 = new User();
            $user2->setUsername("user" . $i);
            $user2->setEmail("mail" . $i);
            $user2->setPassword("password" . $i);
            $user2->setEmploye($employe);
            $user2->setRoles(["ROLE_USER"]);

            $contrat = new Contrats;
            $contrat->setDateDebut(new \DateTime("2015-09-31"));
            $contrat->setDateFin(new \DateTime("2026-09-31"));
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



        $periodeJour = new Periode();
        $periodeJour->setType("30 days");
        $manager->persist($periodeJour);

        $periodeSemaine = new Periode();
        $periodeSemaine->setType("1 year");
        $manager->persist($periodeSemaine);

        $periodeAns = new Periode();
        $periodeAns->setType("5 years");
        $manager->persist($periodeAns);


        //On remplit le stockage home de fausses données
        $resStockageHome = new ResStockagesHome();
        $resStockageHome->setNom("home general");
        $resStockageHome->setPath("/home");
        $resStockageHome->setUser($userUser);

        $valeur = 5;
        for ($i=0; $i < 2000; $i++) {
            $valeur = $this->mesureHome($manager, $resStockageHome, $periodeJour, $valeur, $i, 10);


            //Toutes les semaine on ajoute une mesure
            if ($i % 7 == 0) {
                $valeur = $this->mesureHome($manager, $resStockageHome, $periodeSemaine, $valeur, $i, 10);
            }

            //Tous les ans on ajoute une mesure
            if ($i % 365 == 0) {
                $valeur = $this->mesureHome($manager, $resStockageHome, $periodeAns, $valeur, $i, 10);
            }
        }

        $resStockageHome2 = new ResStockagesHome();
        $resStockageHome2->setNom("home uc");
        $resStockageHome2->setUser($userUser);
        $resStockageHome2->setPath("/home" . $user->getUsername());

        $valeur = 10;
        for ($i=0; $i < 2000; $i++) {
            $valeur = $this->mesureHome($manager, $resStockageHome2, $periodeJour, $valeur, $i, 20);

            //Toutes les semaine on ajoute une mesure
            if ($i % 7 == 0) {
                $valeur = $this->mesureHome($manager, $resStockageHome2, $periodeSemaine, $valeur, $i, 20);
            }

            //Tous les ans on ajoute une mesure
            if ($i % 365 == 0) {
                $valeur = $this->mesureHome($manager, $resStockageHome2, $periodeAns, $valeur, $i, 20);
            }
        }

        $manager->persist($resStockageHome);
        $manager->persist($resStockageHome2);

        //Stockage work
        $resStockageWork = new ResStockageWork();
        $resStockageWork->setNom("apex");
        $resStockageWork->setPath("/work/apex");
        $resStockageWork->setGroupe($groupe2);

        $valeur = 5;
        for ($i=0; $i < 2000; $i++) {
            $valeur = $this->mesureWork($manager, $resStockageWork, $periodeJour, $valeur, $i, 10);

            //Toutes les semaine on ajoute une mesure
            if ($i % 7 == 0) {
                $valeur = $this->mesureWork($manager, $resStockageWork, $periodeSemaine, $valeur, $i, 10);
            }

            //Tous les ans on ajoute une mesure
            if ($i % 365 == 0) {
                $valeur = $this->mesureWork($manager, $resStockageWork, $periodeAns, $valeur, $i, 10);
            }
        }

        $resStockageWork2 = new ResStockageWork();
        $resStockageWork2->setNom("uc");
        $resStockageWork2->setPath("/work/uc");
        $resStockageWork2->setGroupe($groupe);

        $valeur = 10;
        for ($i=0; $i < 2000; $i++) {
            $valeur = $this->mesureWork($manager, $resStockageWork2, $periodeJour, $valeur, $i, 20);

            //Toutes les semaine on ajoute une mesure
            if ($i % 7 == 0) {
                $valeur = $this->mesureWork($manager, $resStockageWork2, $periodeSemaine, $valeur, $i, 20);
            }

            //Tous les ans on ajoute une mesure
            if ($i % 365 == 0) {
                $valeur = $this->mesureWork($manager, $resStockageWork2, $periodeAns, $valeur, $i, 20);
            }
        }


        $manager->persist($resStockageWork);
        $manager->persist($resStockageWork2);

        //On crée des serveurs
        for ($i=0; $i < 20; $i++) {
            //On lui donne un groupe différent de manière aléatoire
            if ($i % 2 == 0) {
                $this->creerServeur($groupe, $employe, $manager, "serveur" . $i);
            } else {
                $this->creerServeur($groupe2, $employe, $manager, "serveur" . $i);
            }
        }


        //On crée un user qui va permettre de communiquer avec les API
        $userApi = new User();
        $userApi->setUsername("api");
        $userApi->setEmail(null);
        $userApi->setPassword('$2y$13$G96OoQ1QoGgglja7bZegW.2a2U4Ryi98PLQuVx.yaGlSq9xeCV5sC');
        $userApi->setRoles(["ROLE_API"]);
        $manager->persist($userApi);

        $userApiMdp = new User();
        $userApiMdp->setUsername("api_mdp");
        $userApiMdp->setEmail(null);
        $userApiMdp->setPassword('$2y$13$3FGx5DvBEdZHsFMyPW4kheasOdycVMNLTud5el4JftId.RE9lH0Ca');
        $userApiMdp->setRoles(["ROLE_API_MDP"]);
        $manager->persist($userApiMdp);

        //On crée des etat Systeme Requete
        $etatSystemeRequete1 = new EtatsRequetes();
        $etatSystemeRequete1->setEtat("D");
        $manager->persist($etatSystemeRequete1);

        $etatSystemeRequete2 = new EtatsRequetes();
        $etatSystemeRequete2->setEtat('C_V');
        $manager->persist($etatSystemeRequete2);

        $etatSystemeRequete3 = new EtatsRequetes();
        $etatSystemeRequete3->setEtat('C_E');
        $manager->persist($etatSystemeRequete3);

        $etatSystemeRequete4 = new EtatsRequetes();
        $etatSystemeRequete4->setEtat('U');
        $manager->persist($etatSystemeRequete4);

        $etatSystemeRequete5 = new EtatsRequetes();
        $etatSystemeRequete5->setEtat('U_V');
        $manager->persist($etatSystemeRequete5);

        $etatSystemeRequete6 = new EtatsRequetes();
        $etatSystemeRequete6->setEtat('U_E');
        $manager->persist($etatSystemeRequete6);

        $manager->flush();
    }

    private function creerServeur($groupe, $employe, $manager, $nomServeur) : void{
        $serveur = new ResServeur();
        $serveur->setNom($nomServeur);
        $groupe->addResServeur($serveur);
        $serveur->addResponsable($employe);


        $derniereMesure = null;
        //On crée des mesures pour le serveur
        for ($i=0; $i < 1000; $i++) {
            $mesure = new ServeursMesures();
            //On rajoute 1 heure à chaque mesure
            $mesure->setDateMesure(new \DateTimeImmutable($i . " hours ago" . " 08:00:00"));

            //On fait légèrement varier les valeurs pour chaque mesure pour simuler des données réelles
            //On récupère la  dernière valeur de la mesure précédente et on lui ajoute un nombre aléatoire entre -3 et 3, sans dépasser la valeur max
            if ($derniereMesure != null && $i % 2 == 0) {
                $cpu = $derniereMesure->getCpu() + rand(-1, 1);
                if ($cpu > 100) {
                    $cpu = 100;
                }
                else if ($cpu < 0) {
                    $cpu = 0;
                }
                $mesure->setCpu($cpu);

                $cpuTotal = $derniereMesure->getCpuTotal() + rand(-1, 1);
                if ($cpuTotal > 100) {
                    $cpuTotal = 100;
                }
                else if ($cpuTotal < 0) {
                    $cpuTotal = 0;
                }
                $mesure->setCpuTotal($cpuTotal);

                $ramUtilise = $derniereMesure->getRamUtilise() + rand(-1, 1);
                if ($ramUtilise > 100) {
                    $ramUtilise = 100;
                }
                else if ($ramUtilise < 0) {
                    $ramUtilise = 0;
                }
                $mesure->setRamUtilise($ramUtilise);

                $ramMax = $derniereMesure->getRamMax() + rand(-1, 1);

                if ($ramMax > 100) {
                    $ramMax = 100;
                }
                else if ($ramMax < 0) {
                    $ramMax = 0;
                }

                $mesure->setRamMax($ramMax);

                $nbUtilisateurs = $derniereMesure->getNbUtilisateurs() + rand(-1, 1);
                if ($nbUtilisateurs < 0) {
                    $nbUtilisateurs = 0;
                }
                $mesure->setNbUtilisateurs($nbUtilisateurs);
            }
            else if ($derniereMesure != null){
                $mesure->setCpu($derniereMesure->getCpu());
                $mesure->setCpuTotal($derniereMesure->getCpuTotal());
                $mesure->setRamUtilise($derniereMesure->getRamUtilise());
                $mesure->setRamMax($derniereMesure->getRamMax());
                $mesure->setNbUtilisateurs($derniereMesure->getNbUtilisateurs());
            }
            else {

                $mesure->setCpu(rand(0, 100));
                $mesure->setCpuTotal(rand(90, 100));
                $mesure->setRamUtilise(rand(0, 100));
                $mesure->setRamMax(rand(90, 100));
                $mesure->setNbUtilisateurs(rand(0, 50));
            }

            $derniereMesure = $mesure;
            $serveur->addMesure($mesure);
            $manager->persist($mesure);
        }

        $manager->persist($serveur);
    }


    private function mesureHome(ObjectManager $manager, $resStockageHome, $periode, $valeur, $i, $max) : int
    {
        $mesure2 = new StockagesMesuresHome();
        $mesure2->setPeriode($periode);
        $resStockageHome->addMesure($mesure2);
        //On remplit toutes les dates à 8h pile et on ajoute $i jours
        $mesure2->setDateMesure(new \DateTimeImmutable($i . " days ago" . " 08:00:00"));
        $valeur += rand(-2, 2);
        if ($valeur > $max) {
            $valeur = $max;
        }
        else if ($valeur < 0) {
            $valeur = 0;
        }
        $mesure2->setValeurUse($valeur);
        $mesure2->setValeurMax($max);
        $manager->persist($mesure2);

        return $valeur;
    }

    public function mesureWork(ObjectManager $manager, $resStockageWork, $periode, $valeur, $i, $max) : int
    {
        $mesure = new StockagesMesuresWork();
        $mesure->setPeriode($periode);
        $resStockageWork->addMesure($mesure);
        $mesure->setDateMesure(new \DateTimeImmutable($i . " days ago" . " 08:00:00"));
        //On fait légèrement varier les valeurs pour chaque mesure pour simuler des données réelles
        //On récupère la valeur de la mesure précédente et on lui ajoute un nombre aléatoire entre -3 et 3, sans dépasser la valeur max
        $valeur = $valeur + rand(-2, 2);
        if ($valeur > $max) {
            $valeur = $max;
        }
        else if ($valeur < 0) {
            $valeur = 0;
        }
        $mesure->setValeurUse($valeur);
        $mesure->setValeurMax($max);

        $manager->persist($mesure);
        return $valeur;
    }
}
