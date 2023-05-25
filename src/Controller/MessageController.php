<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/messages', name: 'app_messages')]
    public function viewMessages(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }
}
