<?php

namespace App\Repository;

use App\Entity\ResStockageWork;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResStockageWork>
 *
 * @method ResStockageWork|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResStockageWork|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResStockageWork[]    findAll()
 * @method ResStockageWork[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResStockageWorkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResStockageWork::class);
    }

    //    /**
    //     * @return ResStockageWork[] Returns an array of ResStockageWork objects
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

    //    public function findOneBySomeField($value): ?ResStockageWork
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
