<?php

namespace App\Repository;

use App\Entity\EtatSystemeRequete;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EtatSystemeRequete>
 *
 * @method EtatSystemeRequete|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtatSystemeRequete|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtatSystemeRequete[]    findAll()
 * @method EtatSystemeRequete[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtatSystemeRequeteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtatSystemeRequete::class);
    }

    //    /**
    //     * @return EtatSystemeRequete[] Returns an array of EtatSystemeRequete objects
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

    //    public function findOneBySomeField($value): ?EtatSystemeRequete
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
