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

    /**
     * Fonction qui permet de récupérer la derniere mesure (la mesure qui a la date la plus récente) de chaque stockage work qui appartient au tableau de GroupesSys donné
     */
    public function findLatestMeasurementsByUser($groupes)
    {
        $qb = $this->createQueryBuilder('m');

        $qb->innerJoin('m.resStockageWork', 'r')
            ->innerJoin('r.groupe', 'g')
            ->where('g IN (:groupes)')
            ->setParameter('groupes', $groupes)
            ->orderBy('m.date_mesure', 'DESC')
            ->groupBy('r.id');

        return $qb->getQuery()->getResult();
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
