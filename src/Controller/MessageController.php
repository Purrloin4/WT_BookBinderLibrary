<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/messages', name: 'app_messages')]
    public function viewMessages(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('messages/messages.html.twig', [
            'controller_name' => 'MessageController',
            'user' => $user,
        ]);
    }
}
