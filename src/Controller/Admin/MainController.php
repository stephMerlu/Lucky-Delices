<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/admin', name: 'index')]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

}
