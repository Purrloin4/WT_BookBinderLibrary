<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private MessageRepository $messageRepository;

    public function __construct(EntityManagerInterface $entityManager, MessageRepository $messageRepository)
    {
        $this->entityManager = $entityManager;
        $this->messageRepository = $messageRepository;
    }

    #[Route('/chat', name: 'chat')]
    public function chat(Request $request): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setSender($this->getUser());
            $message->setCreatedAt(new \DateTime());

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            // Redirect to prevent form resubmission
            return $this->redirectToRoute('chat');
        }

        $messages = $this->messageRepository->findRecentMessages();

        // Get the display name of the user talking to you
        $displayName = $this->getUser() ? $this->getUser()->getDisplayName() : '';

        return $this->render('chat/chat.html.twig', [
            'form' => $form->createView(),
            'messages' => $messages,
            'displayName' => $displayName,
        ]);
    }
}

