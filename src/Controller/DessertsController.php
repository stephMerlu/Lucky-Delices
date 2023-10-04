<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DessertsController extends AbstractController
{
    #[Route('/desserts', name: 'app_desserts')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['name' => 'Les Desserts']);

        return $this->render('entrees/index.html.twig', [
            'category' => $category,
            'image' => 'images/Tarte fraise.jpg'
        ]);
    }
}
