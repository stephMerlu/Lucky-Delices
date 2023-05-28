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
        $aperoRecipes = $recipeRepository->findByCategory("Pour l'apéritif");
        $entreeFroideRecipes = $recipeRepository->findByCategory("Entrée froide");
        $entreeChaudeRecipes = $recipeRepository->findByCategory("Entrée chaude");
        $saucesRecipes = $recipeRepository->findByCategory("Les sauces");
        
    
        return $this->render('entrees/index.html.twig', [
            'controller_name' => 'EntreesController',
            'aperoRecipes' => $aperoRecipes,
            'entreeFroideRecipes' => $entreeFroideRecipes,
            'entreeChaudeRecipes' => $entreeChaudeRecipes,
            'saucesRecipes' => $saucesRecipes,
        ]);
    }
}
