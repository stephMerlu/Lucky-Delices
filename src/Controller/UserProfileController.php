<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\UserProfileType;
use App\Form\EditUserInfoType;
use App\Form\ChangePasswordType;
use App\Repository\UserRepository;
use App\Repository\LikedRepository;
use App\Repository\UserProfileRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProfileController extends AbstractController
{

#[Route('/user/profile', name: 'app_user_profile')]
#[IsGranted("ROLE_USER")]
public function index(Request $request, UserProfileRepository $userProfileRepository, LikedRepository $likedRepository): Response
{
    $user = $this->getUser();
    if (!$user instanceof User) {
        return $this->redirectToRoute('app_login');
    }

    $userProfile = $user->getUserProfile() ?? new UserProfile();

    $form = $this->createForm(UserProfileType::class, $userProfile);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user->setUserProfile($userProfile);
        $userProfileRepository->save($userProfile, true);

        $this->addFlash('success', 'Your profile has been updated.');

        return $this->redirectToRoute('app_home');
    }

    $firstName = null;
    $lastName = null;
    $description = null;
    if ($userProfile instanceof UserProfile) {
        $firstName = $userProfile->getFirstName();
        $lastName = $userProfile->getLastName();
        $description = $userProfile->getDescription();
        $email = $userProfile->getEmail();
    } else {
        $firstName = $user->getUsername();
    }

    $likedRecipes = $likedRepository->findLikedRecipesByUserProfile($userProfile);

    return $this->render('user_profile/index.html.twig', [
        'user' => $user,
        'userProfile' => $userProfile,
        'lastName' => $lastName,
        'firstName' => $firstName,
        'description' => $description ?? null,
        'email' => $email ?? null,
        'form' => $form->createView(),
        'likedRecipes' => $likedRecipes,
    ]);
}


#[Route('/user/profile/edit', name: 'user_profile_edit')]
#[IsGranted("ROLE_USER")]
public function edit(Request $request, UserProfileRepository $userProfileRepository, UserRepository $userRepository): Response
{
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


    #[Route("/profile/change-password", name:"change_password")]
    #[IsGranted("ROLE_USER")]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
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
}
