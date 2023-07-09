<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Repository\UserRepository;
use App\Form\RecipeType;
use Symfony\Component\Mime\Email;
use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/recipe')]
#[Security("is_granted('ROLE_ADMIN')")]

class RecipeController extends AbstractController
{
    #[Route('/', name: 'app_admin_recipe_index', methods: ['GET'])]
    public function index(RecipeRepository $recipeRepository): Response
    {
        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RecipeRepository $recipeRepository, MailerInterface $mailer, UserRepository $userRepository): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $recipeRepository->save($recipe, true);
            $this->sendNewsletter($recipe, $mailer, $userRepository);
            return $this->redirectToRoute('app_admin_recipe_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('admin/recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    private function sendNewsletter(Recipe $recipe, MailerInterface $mailer, UserRepository $userRepository): void
    {
        $users = $userRepository->findBy(['newsletterSubscription' => true]);
    
        foreach ($users as $user) {
            $email = (new Email())
                ->from('hello@example.com')
                ->to($user->getEmail())
                ->subject('New Recipe Newsletter')
                ->html($this->renderView('newsletter/email.html.twig', [
                    'recipe' => $recipe,
                ]));
    
            $mailer->send($email);
        }
    }

    #[Route('/{id}', name: 'app_admin_recipe_show', methods: ['GET'])]
    public function show(Recipe $recipe, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->showCommentsByRecipeId($recipe->getId());
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'comments' => $comments,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_recipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recipe $recipe, RecipeRepository $recipeRepository): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipeRepository->save($recipe, true);

            return $this->redirectToRoute('app_admin_recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_recipe_delete', methods: ['POST'])]
    public function delete(Request $request, Recipe $recipe, RecipeRepository $recipeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $recipe->getId(), $request->request->get('_token'))) {
            $recipeRepository->remove($recipe, true);
        }

        return $this->redirectToRoute('app_admin_recipe_index', [], Response::HTTP_SEE_OTHER);
    }
}
