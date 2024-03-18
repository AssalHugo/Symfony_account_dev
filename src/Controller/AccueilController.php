<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use App\Security\CustomAuthenticator;


class AccueilController extends AbstractController
{
    #[Route('', name: 'accueil')]
    public function index(EntityManagerInterface $entityManager, Security $security): Response
    {

        $user = $entityManager->getRepository(User::class)->findAll()[0];

        $security->login($user, CustomAuthenticator::class);

        return $this->render('accueil/index.html.twig');
    }
}
