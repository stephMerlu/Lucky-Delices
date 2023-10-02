<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlatsController extends AbstractController
{
    #[Route('/plats', name: 'app_plats')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['name' => 'Plat']);

        return $this->render('entrees/index.html.twig', [
            'category' => $category,
            'image' => '/images/tajine.jpg'
        ]);
    }
}
