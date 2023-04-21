<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StarterController extends AbstractController
{
    #[Route('/entrees', name: 'app_entrees')]
    public function index(): Response
    {
        return $this->render('entrees/index.html.twig', [
            'controller_name' => 'EntreesController',
        ]);
    }
}
