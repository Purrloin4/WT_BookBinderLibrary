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
                'controller_name' => 'HomeController', 'user_id' => $user_id, 'recommend' => $isbn, 'bookinfo' => $bookInfo,
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

        // Generate 2 random book IDs
        $isbnArray = [];
        for ($i = 0; $i < 2; ++$i) {
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
}
