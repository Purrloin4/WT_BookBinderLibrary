<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Entity\Subscribe;
use App\Form\CommentMessageFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class BookController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/book/{id}', name: 'book_show', methods: ['POST', 'GET'])]
    public function show(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        // Check if the book information exists in the database
        if (null !== $book->getTitle() && null !== $book->getAuthor() && null !== $book->getBookDescription() && null !== $book->getCoverUrl()) {
            // Book information is available, proceed with the existing data
            return $this->renderBookPage($book, $request, $entityManager);
        }

        $httpClient = HttpClient::create();

        // Fetch book information from the Open Library API
        $isbn = $book->getIsbn();
        $openLibraryResponse = $httpClient->request('GET', "https://openlibrary.org/api/books?bibkeys=ISBN:{$isbn}&format=json&jscmd=data");
        $openLibraryData = $openLibraryResponse->toArray();

        // Extract the book information from the Open Library API response
        $openLibraryBookData = $openLibraryData["ISBN:{$isbn}"] ?? null;
        if (null !== $openLibraryBookData) {
            // Update the book entity with the fetched information
            $book->setTitle($openLibraryBookData['title'] ?? null);
            $book->setAuthor($openLibraryBookData['authors'][0]['name'] ?? null);
            $coverUrl = $openLibraryBookData['cover']['large'] ?? $openLibraryBookData['cover']['medium'] ?? $openLibraryBookData['cover']['small'] ?? null;
            $book->setCoverUrl($coverUrl);

            // Fetch additional book information from the Google Books API
            $googleBooksResponse = $httpClient->request('GET', "https://www.googleapis.com/books/v1/volumes?q=isbn:{$isbn}");
            $googleBooksData = $googleBooksResponse->toArray();

            // Extract the book description from the Google Books API response
            $items = $googleBooksData['items'] ?? [];
            if (count($items) > 0) {
                $item = $items[0];
                $volumeInfo = $item['volumeInfo'] ?? [];

                $book->setBookDescription($volumeInfo['description'] ?? null);
            }

            // Persist the changes to the database
            $entityManager->persist($book);
            $entityManager->flush();
        }

        // Render the book page with the fetched or updated book information
        return $this->renderBookPage($book, $request, $entityManager);
    }

    private function renderBookPage(Book $book, Request $request, EntityManagerInterface $entityManager): Response
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

        $subscribes = $entityManager->getRepository(Subscribe::class)->getSubscribersByBookId($book->getId());
        $subscribers = [];
        foreach ($subscribes as $subscribe) {
            $subscribers[] = $subscribe->getUser();
        }

        $isSubscribed = false;
        $user = $this->getUser();

        if (null !== $user) {
            $isSubscribed = $entityManager->getRepository(Subscribe::class)->isSubscribed($user->getId(), $book->getId());
        }

        return $this->render('book/index.html.twig', [
            'book' => $book,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
            'subscribers' => $subscribers,
            'isSubscribed' => $isSubscribed,
        ]);
    }
}
