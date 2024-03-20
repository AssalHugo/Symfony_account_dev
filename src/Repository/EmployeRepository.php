<?php

namespace App\Repository;

use App\Entity\Employe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    public function findByFiltre($departement, $groupe, $statut): array {

        //ne pas oublier que l'employé peut posséder plusieurs groupes il a les groupes secondaires et sont groupe principal
        $qb = $this->createQueryBuilder('e')
                    ->leftJoin('e.contrats', 'c')
                    ->leftJoin('c.status', 's')
                    ->leftJoin('e.groupe_principal', 'gP')
                    ->leftJoin('e.groupes_secondaires', 'gS')
                    ->leftJoin('gP.departement', 'dP')
                    ->leftJoin('gS.departement', 'dS');



        if (!empty($statut)){
            $qb->Where('s.type LIKE :statut')
                ->setParameter('statut', '%'.$statut.'%');
        }

        if (!empty($groupe)){
            $qb->andWhere('gP.nom LIKE :groupe')
                ->orWhere('gS.nom LIKE :groupe')
                ->setParameter('groupe', '%'.$groupe.'%');
        }

        if (!empty($departement)){
            $qb->andWhere('dP.nom LIKE :departement')
                ->orWhere('dS.nom LIKE :departement')
                ->setParameter('departement', '%'.$departement.'%');
        }

        return $qb->getQuery()->getResult();
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
