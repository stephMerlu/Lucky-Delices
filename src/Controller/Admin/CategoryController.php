<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/category')]
#[Security("is_granted('ROLE_ADMIN')")]

class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_admin_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('admin/category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_category_delete', methods: ['POST'])]
public function delete(
    Request $request, 
    Category $category, 
    CategoryRepository $categoryRepository
    ): Response {
        
    if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
        $categoryRepository->delete($category);
    }

    return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
}

#[Route('/{id}/edit', name: 'app_admin_category_edit', methods: ['GET', 'POST'])]
public function edit(
    Request $request, 
    Category $category, 
    CategoryRepository $categoryRepository
    ): Response {

    $form = $this->createForm(CategoryType::class, $category);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $categoryRepository->save($category);
    
        return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
    }
    
    return $this->renderForm('admin/category/edit.html.twig', [
        'category' => $category,
        'form' => $form,
    ]);
}
}