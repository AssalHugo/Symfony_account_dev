<?php

namespace App\Repository;

use App\Entity\Localisations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Localisations>
 *
 * @method Localisations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Localisations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Localisations[]    findAll()
 * @method Localisations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocalisationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Localisations::class);
    }

    //    /**
    //     * @return Localisations[] Returns an array of Localisations objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Localisations
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
