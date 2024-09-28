<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/reglement', name: 'app_reglement')]
    public function reglement(): Response
    {
        return $this->render('home/reglement.html.twig');
    }

    #[Route('/nous-rejoindre', name: 'join_us')]
    public function join(): Response
    {
        return $this->render('home/join.html.twig');
    }
}
