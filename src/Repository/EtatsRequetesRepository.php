<?php

namespace App\Repository;

use App\Entity\EtatsRequetes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EtatsRequetes>
 *
 * @method EtatsRequetes|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtatsRequetes|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtatsRequetes[]    findAll()
 * @method EtatsRequetes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtatsRequetesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtatsRequetes::class);
    }

    //    /**
    //     * @return EtatsRequetes[] Returns an array of EtatsRequetes objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?EtatsRequetes
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
