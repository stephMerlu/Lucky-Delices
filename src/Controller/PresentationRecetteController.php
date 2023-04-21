<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PresentationRecetteController extends AbstractController
{
    #[Route('/presentation/recette', name: 'app_presentation_recette')]
    public function index(): Response
    {
        return $this->render('presentation_recette/index.html.twig', [
            'controller_name' => 'PresentationRecetteController',
        ]);
    }
}
