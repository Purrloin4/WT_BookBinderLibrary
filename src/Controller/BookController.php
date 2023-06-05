<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Form\CommentMessageFormType;
use App\Form\CommentType;
use App\Form\EditCommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

        $comments = $entityManager->getRepository(Comment::class)->findAll($book->getId());

        return $this->render('book/index.html.twig', [
            'book' => $book,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
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

        $entityManager->remove($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Comment deleted successfully.');

        return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
    }

// TODO: Add a page where all books are shown in a list.
/*
    #[Route('/books', name: 'app_books')]
    public function viewBooks(): Response
    {
        $booksList = ['Book1', 'Book2', 'Book3'];

        return $this->render('books.html.twig', ['controller_name' => 'BookController', 'books_list' => $booksList]);
    }
*/
}

