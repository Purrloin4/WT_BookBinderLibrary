<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book/{id}', name: 'book_show')]
    public function show(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setBook($book);

        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setCommenter($this->getUser());
            $comment->setMessage($commentForm->get('message')->getData());
            $comment->setBook($book);

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Your comment was added!');
        }

        // Fetch the book title from the database
        $bookTitle = $book->getTitle();

        $commentMessages = [];
        $book_id = $book->getId();
        $comments = $entityManager->getRepository(Comment::class)->findAll($book_id);
        foreach ($comments as $comment_item) {
            $commentMessages[] = $comment_item->getMessage();
        }

        return $this->render('book/index.html.twig', [
            'book' => $book,
            'booktitle' => $bookTitle,
            'comments' => $commentMessages,
            'commentForm' => $commentForm->createView(),
            'controller_name' => 'BookController',
        ]);
    }

    // TODO: edit and delete comment
    #[Route('/book/{id}/comment/{comment_id}/edit', name: 'comment_edit')]
    private function editComment(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        // Create a form for editing the comment
        $editForm = $this->createForm(CommentType::class, $comment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $comment->setMessage($editForm->get('message')->getData());

            $entityManager->flush();

            $this->addFlash('success', 'Comment updated successfully!');
            // Redirect to the appropriate page after editing the comment
            return $this->redirectToRoute('book_show', ['id' => $comment->getBook()->getId()]);
        }

        // Render the form for editing the comment
        return $this->render('book/edit_comment.html.twig', [
            'commentForm' => $editForm->createView(),
            'comment' => $comment,
        ]);
    }

    #[Route('/book/{id}/comment/{comment_id}/delete', name: 'comment_delete')]
    private function deleteComment(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        // Create a form for deleting the comment
        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('book_delete_comment', ['id' => $comment->getBook()->getId(), 'comment_id' => $comment->getId()]))
            ->setMethod('DELETE')
            ->getForm();
        $deleteForm->handleRequest($request);

        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Comment deleted successfully!');
            // Redirect to the appropriate page after deleting the comment
            return $this->redirectToRoute('book_show', ['id' => $comment->getBook()->getId()]);
        }

        // Render the form for deleting the comment
        return $this->render('book/delete_comment.html.twig', [
            'deleteForm' => $deleteForm->createView(),
            'comment' => $comment,
        ]);
    }
}
