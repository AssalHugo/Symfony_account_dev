<?php

namespace App\Repository;

use App\Entity\Employe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service_locator;

/**
 * @extends ServiceEntityRepository<Employe>
 *
 * @method Employe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employe[]    findAll()
 * @method Employe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employe::class);
    }

    /**
     * Fonction qui permet de récupérer les employés en fonction des filtres
     */
    public function findByFiltre($nom, $prenom, $departement, $groupe, $statut, $actif): QueryBuilder {

        //ne pas oublier que l'employé peut posséder plusieurs groupes il a les groupes secondaires et sont groupe principal
        $qb = $this->createQueryBuilder('e')
                    ->leftJoin('e.contrats', 'c')
                    ->leftJoin('c.status', 's')
                    ->leftJoin('e.groupe_principal', 'gP')
                    ->leftJoin('e.groupes_secondaires', 'gS')
                    ->leftJoin('gP.departement', 'dP')
                    ->leftJoin('gS.departement', 'dS');


        if (!empty($nom)){
            $qb->Where('e.nom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        }

        if (!empty($prenom)){
            $qb->andWhere('e.prenom LIKE :prenom')
                ->setParameter('prenom', '%'.$prenom.'%');
        }

        if (!empty($statut)){
            $qb->andWhere('s.type LIKE :statut')
                ->setParameter('statut', '%'.$statut.'%');
        }

        if (!empty($groupe)){
            $qb->andWhere('gP.nom LIKE :groupe OR gS.nom LIKE :groupe')
                ->setParameter('groupe', '%'.$groupe.'%');
        }

        if (!empty($departement)){
            $qb->andWhere('dP.nom LIKE :departement OR dS.nom LIKE :departement')
                ->setParameter('departement', '%'.$departement.'%');
        }

        if ($actif != null){
            $qb->andWhere('c.date_debut <= :date')
                ->andWhere('c.date_fin >= :date')
                ->setParameter('date', new \DateTime());
        }

        return $qb;
    }

    /**
     * Fonction qui permet de récupérer tous les employés actifs
     * @return QueryBuilder
     */
    public function findAllEmployesActifs() : QueryBuilder{

        return $this->createQueryBuilder('e')
            ->leftJoin('e.contrats', 'c')
            ->leftJoin('c.status', 's')
            ->leftJoin('e.groupe_principal', 'gP')
            ->Where('c.date_debut <= :date')
            ->andWhere('c.date_fin >= :date')
            ->setParameter('date', new \DateTime());
    }

    public function findByPrenomNom($prenom, $nom): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.contrats', 'c')
            ->andWhere('e.prenom LIKE :prenom')
            ->andWhere('e.nom LIKE :nom')
            ->setParameter('prenom', '%'.$prenom.'%')
            ->setParameter('nom', '%'.$nom.'%')
            ->getQuery()
            ->getResult();
    }


    /**
     * Fonction qui permet de récupérer les employés en fonction des id des groupes, des id des départements et des id des statuts
     */
    public function findByFiltreId($departement, $groupe, $statut): QueryBuilder
    {

        //ne pas oublier que l'employé peut posséder plusieurs groupes il a les groupes secondaires et sont groupe principal
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.contrats', 'c')
            ->leftJoin('c.status', 's')
            ->leftJoin('e.groupe_principal', 'gP')
            ->leftJoin('e.groupes_secondaires', 'gS')
            ->leftJoin('gP.departement', 'dP')
            ->leftJoin('gS.departement', 'dS');

        if (!empty($statut)) {
            $qb->Where('s.id = :statut')
                ->setParameter('statut', $statut);
        }

        if (!empty($groupe)) {
            $qb->andWhere('gP.id = :groupe OR gS.id = :groupe')
                ->setParameter('groupe', $groupe);
        }

        if (!empty($departement)) {
            $qb->andWhere('dP.id = :departement OR dS.id = :departement')
                ->setParameter('departement', $departement);
        }

        return $qb;
    }

    /**
     * Fonction qui retourne le nombre d'employés
     */
    public function countEmployes(): int{

        return $this->createQueryBuilder('e')
                    ->select('count(e.id)')
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    /**
     * Fonction qui retourne le no
     * @return int
     */
    public function countEmployesEntreDates() : int {

        $subQuery = $this->createQueryBuilder('e1')
            ->select('MAX(c1.date_fin)')
            ->innerJoin('e1.contrats', 'c1')
            ->where('e1.id = e.id')
            ->getDQL();

        return $this->createQueryBuilder('e')
            ->select('count(e.id)')
            ->innerJoin('e.contrats', 'c')
            ->where('c.date_debut <= :date')
            ->andWhere('c.date_fin >= :date')
            ->andWhere('c.date_fin = (' . $subQuery . ')')
            ->setParameter('date', new \DateTime())
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return Employe[] Returns an array of Employe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Employe
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
