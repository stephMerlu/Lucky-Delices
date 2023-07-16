<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DessertsController extends AbstractController
{
    #[Route('/desserts', name: 'app_desserts')]
    public function index(RecipeRepository $recipeRepository): Response
    {
        $chocolatRecipes = $recipeRepository->findByCategory("Chocolat");
        $fruitRecipes = $recipeRepository->findByCategory("Fruit");
        $macaronRecipes = $recipeRepository->findByCategory("Macaron");
        $autresGourmandissesRecipes = $recipeRepository->findByCategory("Autres gourmandises");
    
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

        return $this->render('desserts/index.html.twig', [
            'controller_name' => 'DessertsController',
            'chocolatRecipes' => $chocolatRecipes,
            'fruitRecipes' => $fruitRecipes,
            'macaronRecipes' => $macaronRecipes,
            'autresGourmandissesRecipes' => $autresGourmandissesRecipes,
            'likedRecipeIds' => $likedRecipeIds,
        ]);
    }
}
