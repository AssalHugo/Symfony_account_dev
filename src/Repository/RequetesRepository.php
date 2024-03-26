<?php

namespace App\Repository;

use App\Entity\Requetes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Requetes>
 *
 * @method Requetes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Requetes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Requetes[]    findAll()
 * @method Requetes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequetesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Requetes::class);
    }

    /**
     * Fonction qui permet de récupérer les demandes de comptes en fonction de l'état de la requête
     * @param $etat
     * @return array
     */
    public function findByEtatRequete($etat): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.etat_requete', 'e')
            ->Where('e.etat = :etat')
            ->setParameter('etat', $etat)
            ->getQuery()
            ->getResult();
    }

    public function findOrderByDate(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.date_requete', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Requetes[] Returns an array of Requetes objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Requetes
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
