<?php

namespace App\Repository;

use App\Entity\ServeursMesures;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServeursMesures>
 *
 * @method ServeursMesures|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServeursMesures|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServeursMesures[]    findAll()
 * @method ServeursMesures[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServeursMesuresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServeursMesures::class);
    }

    /**
     * Fonction qui permet de récupérer la derniere mesure (la mesure qui a la date la plus récente) de chaque serveyr qui appartient au tableau de Groupes donné
     */
    public function findLatestMeasurementsByUser($groupes) {
        $qb = $this->createQueryBuilder('s');

        $qb->innerJoin('s.resServeur', 'r')
            ->innerJoin('r.groupe', 'g')
            ->where('g IN (:groupes)')
            ->setParameter('groupes', $groupes)
            ->orderBy('s.date_mesure', 'DESC')
            ->groupBy('r.id');

        return $qb->getQuery()->getResult();
    }

    /**
     * Fonction qui permet de récupérer toutes les mesures des serveurs donnés qui n'ont pas comme date de mesure une date inférieure à la date actuelle -30 jours
     */
    public function findlabelEnFonctionDesServeurs(array $serveurs) {
        $qb = $this->createQueryBuilder('s');

        $qb->select('s.date_mesure')
            ->where('s.resServeur IN (:serveurs)')
            ->andWhere('s.date_mesure >= :date')
            ->setParameter('serveurs', $serveurs)
            ->setParameter('date', new \DateTimeImmutable('-30 days'))
            //On trie par date de mesure
            ->orderBy('s.date_mesure', 'ASC')
            ->groupBy('s.date_mesure');

        return $qb->getQuery()->getResult();
    }

    /**
     * Fonction qui permet de récupérer toutes les mesures du serveur donné qui n'ont pas comme date de mesure une date inférieure à la date actuelle -30 jours
     */
    public function findMesuresEnFonctionDuServeur($serveur)
    {
        $qb = $this->createQueryBuilder('s');

        $qb->select('s')
            ->where('s.resServeur = :serveur')
            ->andWhere('s.date_mesure >= :date')
            ->setParameter('serveur', $serveur)
            ->setParameter('date', new \DateTimeImmutable('-30 days'))
            //On trie par date de mesure
            ->orderBy('s.date_mesure', 'ASC')
            ->groupBy('s.date_mesure');

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return ServeursMesures[] Returns an array of ServeursMesures objects
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

    //    public function findOneBySomeField($value): ?ServeursMesures
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
