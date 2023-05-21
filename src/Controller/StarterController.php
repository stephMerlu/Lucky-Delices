<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StarterController extends AbstractController
{
    #[Route('/entrees', name: 'app_entrees')]
    public function index(RecipeRepository $recipeRepository): Response
    {
        $recipes = $recipeRepository->findByCategory("Pour l'apéritif");
        
        return $this->render('entrees/index.html.twig', [
            'controller_name' => 'EntreesController',
            'recipes' => $recipes,
        ]);
    }
}
