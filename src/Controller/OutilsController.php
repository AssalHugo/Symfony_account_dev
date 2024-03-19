<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OutilsController extends AbstractController
{
    /**
     * Ce controlleur permet d'afficher le trombinoscope qui peut etre filtré par département, par groupe, par statut
     * @return Response
     */
    #[Route('/outils/trombinoscope', name: 'trombinoscope')]
    public function index(): Response
    {

        //On

        return $this->render('outils/trombinoscope.html.twig', [
            'controller_name' => 'OutilsController',
        ]);
    }
}
