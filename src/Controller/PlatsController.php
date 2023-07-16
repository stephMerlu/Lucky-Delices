<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlatsController extends AbstractController
{
    #[Route('/plats', name: 'app_plats')]
    public function index(RecipeRepository $recipeRepository): Response
    {
        $pouletRecipes = $recipeRepository->findByCategory("Poulet");
        $boeufRecipes = $recipeRepository->findByCategory("Boeuf");
        $porcRecipes = $recipeRepository->findByCategory("Porc");
        $canardRecipes = $recipeRepository->findByCategory("Canard");
        $poissonRecipes = $recipeRepository->findByCategory("Poisson");
        $vegetarienRecipes = $recipeRepository->findByCategory("Végétarien");

        $user = $this->getUser();
        $likedRecipeIds = [];
        
        if ($user) {
            $likeds = $user->getLikeds();
            $likeds->initialize(); 

            foreach ($likeds as $liked) {
                $likedRecipe = $liked->getRecipe();
                if ($likedRecipe) {
                    $likedRecipeIds[] = $likedRecipe->getId();
                }
            }
        }
    
        return $this->render('plats/index.html.twig', [
            'controller_name' => 'PlatsController',
            'pouletRecipes' => $pouletRecipes,
            'boeufRecipes' => $boeufRecipes,
            'porcRecipes' => $porcRecipes,
            'canardRecipes' => $canardRecipes,
            'poissonRecipes' => $poissonRecipes,
            'vegetarienRecipes' => $vegetarienRecipes,
            'likedRecipeIds' => $likedRecipeIds,
        ]);
    }
}
