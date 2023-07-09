<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\Recipe;
use App\Entity\Liked;
use App\Entity\Comment;
use App\Form\UserProfileType;
use App\Form\EditUserInfoType;
use App\Form\ChangePasswordType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use App\Repository\LikedRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserProfileRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile')]
    #[IsGranted("ROLE_USER")]
    public function index(
        Request $request,
        UserRepository $userRepository,
        UserProfileRepository $userProfileRepository,
        LikedRepository $likedRepository
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
    
        $userProfile = $user->getUserProfile() ?? new UserProfile();
    
        $form = $this->createForm(UserProfileType::class, $userProfile);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUserProfile($userProfile);
            $userRepository->save($user, true);
            $this->addFlash('success', 'Your profile has been updated.');
    
            return $this->redirectToRoute('app_home');
        }
    
        $firstName = null;
        $lastName = null;
        $description = null;
        $email = null;
        $newsletterSubscription = false;
        if ($userProfile instanceof UserProfile) {
            $firstName = $userProfile->getFirstName();
            $lastName = $userProfile->getLastName();
            $description = $userProfile->getDescription();
            $email = $userProfile->getEmail();
            $newsletterSubscription = $user->isNewsletterSubscription();
        } else {
            $firstName = $user->getUsername();
            $newsletterSubscription = $user->isNewsletterSubscription();
        }
    
        $likedRecipes = $likedRepository->findLikedRecipesByUserProfile($userProfile);
    
        return $this->render('user_profile/index.html.twig', [
            'user' => $user,
            'userProfile' => $userProfile,
            'lastName' => $lastName,
            'firstName' => $firstName,
            'description' => $description ?? null,
            'email' => $email ?? null,
            'newsletterSubscription' => $newsletterSubscription,
            'form' => $form->createView(),
            'likedRecipes' => $likedRecipes,
        ]);
    }

    #[Route('/user/profile/edit', name: 'user_profile_edit')]
    #[IsGranted("ROLE_USER")]
    public function edit(
        Request $request,
        UserProfileRepository $userProfileRepository,
        UserRepository $userRepository
    ): Response {
        $user = $this->getUser();

        $userProfile = $user->getUserProfile();
        if (!$userProfile) {
            $userProfile = new UserProfile();
            $userProfile->setUser($user);
            $user->setUserProfile($userProfile);
        }

        $userProfileForm = $this->createForm(UserProfileType::class, $userProfile);
        $userInfoForm = $this->createForm(EditUserInfoType::class, $user);

        $userProfileForm->handleRequest($request);
        $userInfoForm->handleRequest($request);

        if ($userProfileForm->isSubmitted() && $userProfileForm->isValid()) {
            $userProfileRepository->save($userProfile, true);
            $this->addFlash('success', 'Your profile has been updated.');
            return $this->redirectToRoute('app_user_profile');
        }

        if ($userInfoForm->isSubmitted() && $userInfoForm->isValid()) {
            $userRepository->save($user, true);
            $this->addFlash('success', 'User info has been updated.');
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user_profile/edit.html.twig', [
            'user' => $user,
            'userProfileForm' => $userProfileForm->createView(),
            'userInfoForm' => $userInfoForm->createView(),
        ]);
    }

    #[Route("/profile/change-password", name: "change_password")]
    #[IsGranted("ROLE_USER")]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ): Response {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $userRepository->save($user, true);
            $this->addFlash('success', 'Password updated!');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('user_profile/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/presentation/recipe/{recipeId}', name: 'presentation_recipe', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_USER")]
    public function showRecipe(int $recipeId, Request $request, RecipeRepository $recipeRepository, CommentRepository $commentRepository, EntityManagerInterface $entityManager): Response 
    {
        $recipe = $recipeRepository->find($recipeId);
        $comments = $commentRepository->findBy([
            'validated' => true,
            'commentRecipe' => $recipe
        ]);
        
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setCommentRecipe($recipe);
            $entityManager->persist($comment);
            $entityManager->flush();
            
            $this->addFlash('success', 'Comment added successfully!');
            
            return $this->redirectToRoute('presentation_recipe', ['recipeId' => $recipeId]);
        }
        
        return $this->render('presentation_recette/index.html.twig', [
            'recipe' => $recipe,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
        ]);
    }
    
    

    #[Route('/like-recipe/{recipeId}', name: 'like_recipe', methods: ['POST', 'DELETE'])]
    #[IsGranted("ROLE_USER")]
    public function likeRecipe(
        Request $request,
        int $recipeId,
        LikedRepository $likedRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $user = $this->getUser();
        $recipe = $entityManager->getRepository(Recipe::class)->find($recipeId);

        if (!$user || !$recipe) {
            return $this->json(['success' => false, 'error' => 'User or recipe not found']);
        }

        $liked = $likedRepository->findOneBy(['user' => $user, 'recipe' => $recipe]);

        if ($request->isMethod('POST')) {
            if (!$liked) {
                $liked = new Liked();
                $liked->setUser($user);
                $liked->setRecipe($recipe);

                $likedRepository->save($liked, true);
            }
        } elseif ($request->isMethod('DELETE')) {
            if ($liked) {
                $likedRepository->remove($liked, true);
            }
        }

        return $this->json(['success' => true]);
    }

    #[Route('/unsubscribe-newsletter', name: 'unsubscribe_newsletter')]
    public function unsubscribeNewsletter(UserRepository $userRepository): Response
    {
        $user = $this->getUser();
    
        if ($user instanceof User) {
            $user->setNewsletterSubscription(false);
            $userRepository->save($user, true);
    
            // Affichez un message de confirmation ou redirigez l'utilisateur vers une autre page
            $this->addFlash('success', 'Vous avez été désinscrit de la newsletter avec succès.');
        }
    
        return $this->redirectToRoute('app_user_profile');
    }
}

