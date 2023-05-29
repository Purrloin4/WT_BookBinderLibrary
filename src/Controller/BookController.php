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

        return $this->render('book/index.html.twig', [
            'book' => $book,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    // Todo: edit and delete comment
}
