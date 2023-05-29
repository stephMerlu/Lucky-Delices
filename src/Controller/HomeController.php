<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(RecipeRepository $recipeRepository): Response
    {
        $recentRecipes = $recipeRepository->findRecentRecipes(3);

        return $this->render('home/index.html.twig', [
            'recipes' => $recentRecipes,
        ]);
    }

    #[Route("/recipes/recent", name: "recent_recipes", methods: ["GET"])]
    public function getRecentRecipes(RecipeRepository $recipeRepository, UploaderHelper $uploaderHelper): JsonResponse
    {
        $recentRecipes = $recipeRepository->findRecentRecipes(3);

        $jsonData = [];
        foreach ($recentRecipes as $recipe) {
            $jsonData[] = [
                'id' => $recipe->getId(),
                'name' => $recipe->getName(),
                'subtitle' => $recipe->getSubtitle(),
                'image' => $uploaderHelper->asset($recipe, 'imageFile'),
                'url' => $this->generateUrl('app_presentation_recette', ['id' => $recipe->getId()]),
            ];
        }

        return new JsonResponse($jsonData);
    }

    #[Route("/recipes/recent/render", name: "render_recent_recipes", methods: ["GET"])]
    public function renderRecentRecipes(RecipeRepository $recipeRepository): Response
    {
        $recentRecipes = $recipeRepository->findRecentRecipes(3);

        return $this->render('home/index.html.twig', [
            'recipes' => $recentRecipes,
        ]);
    }
}
