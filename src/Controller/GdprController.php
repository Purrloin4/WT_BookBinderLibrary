<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GdprController extends AbstractController
{
    #[Route('/gdpr', name: 'app_gdpr')]
    public function index(): Response
    {
        return $this->render('gdpr/index.html.twig', [
            'controller_name' => 'GdprController',
        ]);
    }
}
