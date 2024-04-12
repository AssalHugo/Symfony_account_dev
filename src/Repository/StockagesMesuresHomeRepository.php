<?php

namespace App\Repository;

use App\Entity\StockagesMesuresHome;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockagesMesuresHome>
 *
 * @method StockagesMesuresHome|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockagesMesuresHome|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockagesMesuresHome[]    findAll()
 * @method StockagesMesuresHome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockagesMesuresHomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockagesMesuresHome::class);
    }

    /**
     * Fonction qui permet de récupérer la derniere mesure (la mesure qui a la date la plus récente) de chaque stockage home qui appartient à un utilisateur donné
     */
    public function findLatestMeasurementsByUser($userId)
    {
        $qb = $this->createQueryBuilder('s');

        $qb ->innerJoin('s.resStockagesHomes', 'r')
            ->innerJoin('r.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('s.date_mesure', 'DESC')
            ->groupBy('r.id');

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return StockagesMesuresHome[] Returns an array of StockagesMesuresHome objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?StockagesMesuresHome
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
