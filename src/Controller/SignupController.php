<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SignupController extends AbstractController
{
    #[Route("/signup", name: "signup")]
    public function signup(): Response
    {
        return $this->render('signup.html.twig');
    }
}   