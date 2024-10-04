<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExercicesController extends AbstractController
{
    #[Route('/exercices', name: 'app_exercices')]
    public function index(): Response
    {
        return $this->render('exercices/index.html.twig', [
            'controller_name' => 'ExercicesController',
        ]);
    }

    #[Route('/add', name: 'app_exercices_add')]
    public function add(): Response
    {
        return $this->render('exercices/index.html.twig', [
            'controller_name' => 'ExercicesController',
        ]);
    }
}
