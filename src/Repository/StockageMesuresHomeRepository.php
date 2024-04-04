<?php

namespace App\Repository;

use App\Entity\StockageMesuresHome;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockageMesuresHome>
 *
 * @method StockageMesuresHome|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockageMesuresHome|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockageMesuresHome[]    findAll()
 * @method StockageMesuresHome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockageMesuresHomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockageMesuresHome::class);
    }

    //    /**
    //     * @return StockageMesuresHome[] Returns an array of StockageMesuresHome objects
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

    //    public function findOneBySomeField($value): ?StockageMesuresHome
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
