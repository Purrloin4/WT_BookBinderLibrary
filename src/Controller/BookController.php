<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Form\CommentMessageFormType;
use Doctrine\ORM\EntityManagerInterface;
use MongoDB\Driver\Monitoring\Subscriber;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Node\Expression\Binary\SubBinary;

class BookController extends AbstractController
{
    #[Route('/book/{id}', name: 'book_show', methods: ['POST', 'GET'])]
    public function show(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $commentForm = $this->createForm(CommentMessageFormType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setCommenter($this->getUser());
            $comment->setMessage($commentForm->get('message')->getData());
            $comment->setBook($book);

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Your comment was added!');
        }

        $comments = $entityManager->getRepository(Comment::class)->getCommentsByBookId($book->getId());

        $subscribers = $entityManager->getRepository(Subscriber::class)->getSubscribersByBookId($book->getId());

        return $this->render('book/index.html.twig', [
            'book' => $book,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
            'subscribers' => $subscribers,
        ]);
    }

    #[Route('/book/{id}/comment/{commentId}/edit', name: 'comment_edit', methods: ['POST', 'GET'])]
    public function edit(Request $request, Book $book, int $commentId, EntityManagerInterface $entityManager): Response
    {
        $comment = $entityManager->getRepository(Comment::class)->find($commentId);

        if (!$comment) {
            throw $this->createNotFoundException('Comment not found');
        }

        $form = $this->createForm(CommentMessageFormType::class, $comment, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the updated comment to the database
            $comment->setMessage($form->get('message')->getData());
            $comment->setEdited(true);
            // Persist the changes to the comment
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Comment updated successfully.');

            return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
        }

        return $this->render('book/index.html.twig', [
            'book' => $book,
            'editComment' => $comment,
            'comments' => $entityManager->getRepository(Comment::class)->findAll($book->getId()),
            'editingComment' => true,
            'editCommentForm' => $form->createView(),
        ]);
    }

    #[Route('/book/{id}/comment/{commentId}/delete', name: 'comment_delete', methods: ['GET'])]
    public function delete(Request $request, Book $book, int $commentId, EntityManagerInterface $entityManager): Response
    {
        $comment = $entityManager->getRepository(Comment::class)->find($commentId);

        if (!$comment) {
            throw $this->createNotFoundException('Comment not found');
        }

        // Check if the current user is the owner of the comment
        $currentUser = $this->getUser();
        if ($comment->getCommenter() !== $currentUser) {
            throw $this->createAccessDeniedException('You are not allowed to delete this comment.');
        }

        $entityManager->remove($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Comment deleted successfully.');

        return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
    }

    #[Route('/book/{id}/subscribe', name: 'book_subscribe', methods: ['POST'])]
    public function subscribe(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();

        if (!$currentUser) {
            throw $this->createAccessDeniedException('You must be logged in to subscribe to a book.');
        }

        if ($book->isUserSubscribed($currentUser)) {
            $book->removeSubscriber($currentUser);
            $message = 'You have unsubscribed from this book.';
        } else {
            $book->addSubscriber($currentUser);
            $message = 'You have subscribed to this book.';
        }

        $entityManager->persist($book);
        $entityManager->flush();

        $this->addFlash('success', $message);

        return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
    }

    #[Route('/book/{id}/unsubscribe', name: 'book_unsubscribe', methods: ['POST'])]
    public function unsubscribe(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();

        if (!$currentUser) {
            throw $this->createAccessDeniedException('You must be logged in to unsubscribe from a book.');
        }

        if ($book->isUserSubscribed($currentUser)) {
            $book->removeSubscriber($currentUser);
            $message = 'You have unsubscribed from this book.';
        } else {
            throw $this->createAccessDeniedException('You are not subscribed to this book.');
        }

        $entityManager->persist($book);
        $entityManager->flush();

        $this->addFlash('success', $message);

        return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
    }

}

