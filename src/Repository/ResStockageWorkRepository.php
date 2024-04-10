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

    /**
     * Fonction qui permet de récupérer la derniere mesure (la mesure qui a la date la plus récente) de chaque stockage work qui appartient au tableau de GroupesSys donné
     */
    public function findLatestMeasurementsByUser($groupesSysId)
    {
        $qb = $this->createQueryBuilder('r');

        $qb->select('m.id')
            ->innerJoin('r.mesure', 'm')
            ->where('r.groupeSys = :groupesSysId')
            ->setParameter('groupesSysId', $groupesSysId)
            ->orderBy('m.date_mesure', 'DESC')
            ->groupBy('r.id');

        return $qb->getQuery()->getResult();
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
