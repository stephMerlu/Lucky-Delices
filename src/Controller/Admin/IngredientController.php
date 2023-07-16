<?php

namespace App\Controller\Admin;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/ingredient')]
#[Security("is_granted('ROLE_ADMIN')")]

class IngredientController extends AbstractController
{
    #[Route('/', name: 'app_admin_ingredient_index', methods: ['GET'])]
    public function index(IngredientRepository $ingredientRepository): Response
    {
        return $this->render('admin/ingredient/index.html.twig', [
            'ingredients' => $ingredientRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_ingredient_new', methods: ['GET', 'POST'])]
    public function new(Request $request, IngredientRepository $ingredientRepository): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredientRepository->save($ingredient, true);

            return $this->redirectToRoute('app_admin_ingredient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/ingredient/new.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_ingredient_show', methods: ['GET'])]
    public function show(Ingredient $ingredient): Response
    {
        return $this->render('admin/ingredient/show.html.twig', [
            'ingredient' => $ingredient,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_ingredient_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Ingredient $ingredient, 
        IngredientRepository $ingredientRepository
        ): Response {

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredientRepository->save($ingredient, true);

            return $this->redirectToRoute('app_admin_ingredient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/ingredient/edit.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_ingredient_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Ingredient $ingredient, 
        IngredientRepository $ingredientRepository
        ): Response {
            
        if ($this->isCsrfTokenValid('delete'.$ingredient->getId(), $request->request->get('_token'))) {
            $ingredientRepository->remove($ingredient, true);
        }

        return $this->redirectToRoute('app_admin_ingredient_index', [], Response::HTTP_SEE_OTHER);
    }
}
