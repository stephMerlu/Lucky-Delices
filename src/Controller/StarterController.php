<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StarterController extends AbstractController
{
    #[Route('/entrees', name: 'app_entrees')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['name' => 'Entrée']);

        return $this->render('entrees/index.html.twig', [
            'category' => $category,
            'image' => '/images/Le soleil pesto.jpg'
        ]);
    }
}
