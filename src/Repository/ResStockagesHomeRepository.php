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
