<?php

namespace App\Service;

use App\Entity\Employe;
use Doctrine\ORM\EntityManagerInterface;

class GroupesService
{

    public function getEmployeNonGroupe($groupe, EntityManagerInterface $entityManager) : array {

        $employes = $entityManager->getRepository(Employe::class)->findAll();

        $employesGroupePrincipaux = $groupe->getEmployesGrpPrincipaux()->toArray();

        $employesGroupeSecondaires = $groupe->getEmployeGrpSecondaires()->toArray();

        $employesGroupe = array_merge($employesGroupePrincipaux, $employesGroupeSecondaires);

        return array_udiff($employes, $employesGroupe,
            function ($obj_a, $obj_b) {
                return $obj_a->getId() - $obj_b->getId();
            }
        );
    }
}