<?php

namespace App\Repository;

use App\Entity\Batiments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Batiments>
 *
 * @method Batiments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Batiments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Batiments[]    findAll()
 * @method Batiments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BatimentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Batiments::class);
    }

    //    /**
    //     * @return Batiments[] Returns an array of Batiments objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Batiments
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
