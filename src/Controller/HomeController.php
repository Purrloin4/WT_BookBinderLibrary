<?php

namespace App\Controller;

use App\Entity\Book;
use App\Service\GetBookInfo;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_root')]
    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager, GetBookInfo $bookInfo): Response
    { // this function pass the recommended books' isbn to the home page and render the page
        $isbn = $this->get_recommendation($entityManager);

        //  dump($isbn); // Add this line to display the content of $isbn

        if (null != $this->getUser()) {
            $user_id = $this->getUser()->getId();

            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController', 'user_id' => $user_id, 'recommend' => $isbn,
            ]);
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController', 'recommend' => $isbn, 'bookinfo' => $bookInfo,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws \Exception
     */
    private function get_recommendation(EntityManagerInterface $entityManager): array
    {
        $repository = $entityManager->getRepository(Book::class);

        // Get the maximum book ID
        $bookId_max = $repository->createQueryBuilder('b')
            ->select('MAX(b.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Generate 8 random book IDs
        $isbnArray = [];
        for ($i = 0; $i < 8; ++$i) {
            // Generate a random book ID
            $bookId = random_int(1, $bookId_max);

            // Retrieve the book with the generated ID
            $book = $repository->findOneBy(['id' => $bookId]);

            if (!$book) {
                throw $this->createNotFoundException('No book found');
            }

            $isbnArray[] = $book->getIsbn();
        }

        return $isbnArray;
    }

    public function getBookCover(string $isbn): string
    {
        $url = 'https://covers.openlibrary.org/b/isbn/'.$isbn.'-L.jpg';

        // Check if the image is too small
        $imageSize = @getimagesize($url);
        if (false !== $imageSize && ($imageSize[0] < 200 || $imageSize[1] < 200)) {
            // Return a blank image URL
            $url = 'https://ps.w.org/replace-broken-images/assets/icon-256x256.png?rev=2561727';
        }

        return $url;
    }

    public function getBookTitle(string $isbn): string
    {
        $url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:'.$isbn.'&key=AIzaSyBE4Jpq7yFvTEZtgcX4ONKRz0ZMbIt397w';
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        if (empty($data['items'][0]['volumeInfo']['title'])) {
            return 'No title';
        }

        return $data['items'][0]['volumeInfo']['title'];
    }

    public function getBookAuthor(string $isbn): string
    {
        $url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:'.$isbn.'&key=AIzaSyBE4Jpq7yFvTEZtgcX4ONKRz0ZMbIt397w';
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        if (empty($data['items'][0]['volumeInfo']['authors'][0])) {
            return 'No author';
        }

        return $data['items'][0]['volumeInfo']['authors'][0];
    }

    public function getBookRate(string $isbn): string
    {
        $url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:'.$isbn.'&key=AIzaSyBE4Jpq7yFvTEZtgcX4ONKRz0ZMbIt397w';
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        if (empty($data['items'][0]['volumeInfo']['averageRating'])) {
            return 'No rate';
        }

        return $data['items'][0]['volumeInfo']['averageRating'];
    }

    public function getBookSummary(string $isbn): string
    {
        $url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:'.$isbn.'&key=AIzaSyBE4Jpq7yFvTEZtgcX4ONKRz0ZMbIt397w';
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        if (empty($data['items'][0]['volumeInfo']['description'])) {
            return 'No summary';
        }

        return $data['items'][0]['volumeInfo']['description'];
    }
}
