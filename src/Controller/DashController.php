<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashController extends AbstractController
{
    #[Route("/dash", name: "dash_page")]
    public function dash(): Response
    {
        return $this->render('back/dashboard.html.twig');
    }
}