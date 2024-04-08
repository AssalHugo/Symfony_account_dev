<?php

namespace App\Repository;

use App\Entity\GroupesSys;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupesSys>
 *
 * @method GroupesSys|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupesSys|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupesSys[]    findAll()
 * @method GroupesSys[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupesSysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupesSys::class);
    }

//    /**
//     * @return GroupesSys[] Returns an array of GroupesSys objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GroupesSys
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
