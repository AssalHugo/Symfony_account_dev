<?php

namespace App\Repository;

use App\Entity\ServeurMesures;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServeurMesures>
 *
 * @method ServeurMesures|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServeurMesures|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServeurMesures[]    findAll()
 * @method ServeurMesures[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServeurMesuresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServeurMesures::class);
    }

    //    /**
    //     * @return ServeurMesures[] Returns an array of ServeurMesures objects
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

    //    public function findOneBySomeField($value): ?ServeurMesures
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
