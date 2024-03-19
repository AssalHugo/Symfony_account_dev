<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OutilsController extends AbstractController
{
    #[Route('/outils/trombinoscope', name: 'trombinoscope')]
    public function index(): Response
    {



        return $this->render('outils/index.html.twig', [
            'controller_name' => 'OutilsController',
        ]);
    }
}
