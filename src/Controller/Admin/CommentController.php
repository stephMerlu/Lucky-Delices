<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Form\CommentAdminType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/comment')]
#[Security("is_granted('ROLE_ADMIN')")]

class CommentController extends AbstractController
{
    #[Route('/', name: 'app_admin_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy([], ['createdAt' => 'DESC']);
        
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $comments,
        ]);
    }
    

    #[Route('/new/{CommentRecipe}', name: 'app_admin_comment_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        CommentRepository $commentRepository, 
        $recipeId
        ): Response {

        $comment = new Comment();
        $recipe = $commentRepository->getEntityManager()->getRepository('App:Recipe')->find($recipeId);
        $comment->setRecipe($recipe);
        $form = $this->createForm(CommentAdminType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->save($comment, true);

            return $this->redirectToRoute('app_admin_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('admin/comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_comment_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Comment $comment, 
        CommentRepository $commentRepository
        ): Response {

        $form = $this->createForm(CommentAdminType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->save($comment, true);

            return $this->redirectToRoute('app_admin_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_comment_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Comment $comment, 
        CommentRepository $commentRepository
        ): Response {
            
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment, true);
        }

        return $this->redirectToRoute('app_admin_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}

