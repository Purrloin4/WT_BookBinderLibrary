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

    #[Route('/chat/{id}', name: 'chat')]
    public function chat(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Retrieve the latest chat history for the given user
        $messages = $this->messageRepository->findBySenderAndReceiver($this->getUser(), $user);

        // Create a new message form
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Set the sender and receiver of the message
            $message->setSender($this->getUser());
            $message->setReceiver($user);

            // Persist the message in the database
            $this->entityManager->persist($message);
            $this->entityManager->flush();

            // Redirect to the chat page to display the updated chat history
            return $this->redirectToRoute('chat', ['id' => $user->getId()]);
        }

        return $this->render('chat/chat.html.twig', [
            'user' => $user,
            'messages' => $messages,
            'messageForm' => $form->createView(),
        ]);
    }

}

