<?php

namespace App\Repository;

use App\Entity\ResServeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResServeur>
 *
 * @method ResServeur|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResServeur|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResServeur[]    findAll()
 * @method ResServeur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResServeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResServeur::class);
    }

    /**
     * Fonction qui permet de récupérer les ressources ser qui serveur artiennent à des groupes donnés
     * @param $groupes
     * @return mixed
     */
    public function findByGroupes($groupes){

        $qb = $this->createQueryBuilder('r');

        $qb->innerJoin('r.groupe', 'g')
            ->where('g IN (:groupes)')
            ->setParameter('groupes', $groupes);

        return $qb->getQuery()->getResult();
    }

    public function findServeurIdNomAvecGroupe($groupe){

        $qb = $this->createQueryBuilder('r');

        $qb->select('r.id', 'r.nom')
            ->where('r.groupe = :groupe')
            ->setParameter('groupe', $groupe);

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return ResServeur[] Returns an array of ResServeur objects
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

    //    public function findOneBySomeField($value): ?ResServeur
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
