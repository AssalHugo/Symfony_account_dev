<?php

namespace App\Repository;

use App\Entity\ResStockagesHome;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResStockagesHome>
 *
 * @method ResStockagesHome|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResStockagesHome|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResStockagesHome[]    findAll()
 * @method ResStockagesHome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResStockagesHomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResStockagesHome::class);
    }

    /**
     * Fonction qui permet de récupérer la derniere mesure (la mesure qui a la date la plus récente) de chaque stockage home qui appartient à un utilisateur donné
     */
    public function findLatestMeasurementsByUser($userId)
    {
        $qb = $this->createQueryBuilder('r');

        $qb->select('m.id')
            ->innerJoin('r.mesures', 'm')
            ->where('r.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('m.date_mesure', 'DESC')
            ->groupBy('r.id');

        return $qb->getQuery()->getResult();
    }


    //    /**
    //     * @return ResStockagesHome[] Returns an array of ResStockagesHome objects
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

    //    public function findOneBySomeField($value): ?ResStockagesHome
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
