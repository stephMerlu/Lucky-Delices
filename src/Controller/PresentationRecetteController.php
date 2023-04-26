<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\RecipeRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PresentationRecetteController extends AbstractController
{
    #[Route('/presentation/recette', name: 'app_presentation_recette')]
    public function index(Request $request, RecipeRepository $recipeRepository, CommentRepository $commentRepository): Response
    {
        $recipes = $recipeRepository->findAll();

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $commentRepository->save($comment, true);
            $this->addFlash('success', 'Comment added successfully!');
            return $this->redirectToRoute('app_presentation_recette');
        }

        // récupération des commentaires validés
        $comments = $commentRepository->findBy([
            'validated' => true,
        ]);

        return $this->render('presentation_recette/index.html.twig', [
            'controller_name' => 'PresentationRecetteController',
            'recipes' => $recipes,
            'commentForm' => $form->createView(),
            'comments' => $comments,
        ]);
    }
}


