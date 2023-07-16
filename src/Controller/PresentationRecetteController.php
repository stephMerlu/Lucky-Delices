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
    #[Route('/presentation/recette/{id}', name: 'app_presentation_recette')]
    public function index(
        Request $request, 
        RecipeRepository $recipeRepository, 
        CommentRepository $commentRepository, 
        int $id
        ): Response {
        $recipe = $recipeRepository->find($id);
        
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setCommentRecipe($recipe);            
            $commentRepository->save($comment, true);
            $this->addFlash('success', 'Comment added successfully!');
            return $this->redirectToRoute('app_presentation_recette', ['id' => $id]);
        }

        $comments = $commentRepository->findBy([
            'validated' => true,
            'commentRecipe' => $recipe
        ]);

        return $this->render('presentation_recette/index.html.twig', [
            'controller_name' => 'PresentationRecetteController',
            'recipe' => $recipe,
            'commentForm' => $form->createView(),
            'comments' => $comments,
        ]);
    }
}