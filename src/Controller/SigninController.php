<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SigninController extends AbstractController
{
    #[Route("/signin", name: "signin")]
    public function signin(): Response
    {
        return $this->render('signin.html.twig');
    }
}