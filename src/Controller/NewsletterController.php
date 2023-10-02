<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\NewsletterRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class NewsletterController extends AbstractController
{
    private $mailer;
    private $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    #[Route('/newsletter', name: 'app_newsletter')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $form = $this->createForm(NewsletterType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
    
            $existingUser = $userRepository->findOneBy(['email' => $email]);
    
            if ($existingUser) {
                if (!$existingUser->isNewsletterSubscription()) {
                    $existingUser->setNewsletterSubscription(true);
                    $userRepository->save($existingUser, true);
                }
    
                $this->sendConfirmationEmail($email);
    
                return $this->redirectToRoute('app_newsletter', ['success' => 'subscribed']);
            }
    
            return $this->redirectToRoute('app_newsletter', ['error' => 'user_not_found']);
        }
    
        return $this->render('newsletter/index.html.twig', [
            'controller_name' => 'NewsletterController',
            'form' => $form->createView(),
        ]);
    }
    

    private function sendConfirmationEmail(string $email)
    {
        $message = (new Email())
            ->from('newsletter@example.com')
            ->to($email)
            ->subject("Confirmation d'inscription à la newsletter")
            ->html($this->twig->render('newsletter/confirmation.html.twig'));

        $this->mailer->send($message);
    }
}
