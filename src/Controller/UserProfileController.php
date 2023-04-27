<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\UserProfileType;
use App\Repository\UserProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile')]
    #[IsGranted("ROLE_USER")]
    public function index(Request $request, UserProfileRepository $userProfileRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            // Rediriger vers la page de connexion ou afficher un message d'erreur
            return $this->redirectToRoute('app_login');
        }
    
        $userProfile = $user->getUserProfile() ?? new UserProfile();
    
        $form = $this->createForm(UserProfileType::class, $userProfile);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUserProfile($userProfile);
            $userProfileRepository->save($userProfile, true);
    
            $this->addFlash('success', 'Your profile has been updated.');
    
            return $this->redirectToRoute('app_user_profile');
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
    
        return $this->render('user_profile/index.html.twig', [
            'user' => $user,
            'userProfile' => $userProfile,
            'lastName' => $lastName,
            'firstName' => $firstName,
            'description' => $description ?? null,
            'email' => $email ?? null,
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/user/profile/edit', name: 'user_profile_edit')]
    #[IsGranted("ROLE_USER")]
    public function edit(Request $request, UserProfileRepository $userProfileRepository): Response
    {
        $user = $this->getUser();
        $userProfile = $user->getUserProfile() ?? new UserProfile();

        $form = $this->createForm(UserProfileType::class, $userProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUserProfile($userProfile);
            $userProfileRepository->save($userProfile, true);

            $this->addFlash('success', 'Your profile has been updated.');

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user_profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
