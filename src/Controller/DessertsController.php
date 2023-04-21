<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DessertsController extends AbstractController
{
    #[Route('/desserts', name: 'app_desserts')]
    public function index(): Response
    {
        return $this->render('desserts/index.html.twig', [
            'controller_name' => 'DessertsController',
        ]);
    }
}
