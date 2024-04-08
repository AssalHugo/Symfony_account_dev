<?php

namespace App\Repository;

use App\Entity\StockagesMesuresWork;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockagesMesuresWork>
 *
 * @method StockagesMesuresWork|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockagesMesuresWork|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockagesMesuresWork[]    findAll()
 * @method StockagesMesuresWork[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockagesMesuresWorkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockagesMesuresWork::class);
    }

    //    /**
    //     * @return StockagesMesuresWork[] Returns an array of StockagesMesuresWork objects
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

    //    public function findOneBySomeField($value): ?StockagesMesuresWork
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
