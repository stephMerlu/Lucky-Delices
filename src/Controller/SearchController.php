<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{

    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function searchResults(Request $request, RecipeRepository $recipeRepository): Response
    {
        $searchTerm = $request->query->get('search');
        $searchBy = $request->query->get('searchBy');

        $results = $recipeRepository->searchRecipes($searchTerm, $searchBy);

        return $this->render('search/index.html.twig', [
            'results' => $results,
            'searchTerm' => $searchTerm,
            'searchBy' => $searchBy,
        ]);
    }
}
