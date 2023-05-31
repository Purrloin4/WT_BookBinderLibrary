<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_root')]
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        $isbn13 = $this->home_recommendation();

        dump($isbn13); // Add this line to display the content of $isbn13

        if (null != $this->getUser()) {
            $user_id = $this->getUser()->getId();

            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController', 'user_id' => $user_id, 'recommend' => $isbn13,
            ]);
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController', 'recommend' => $isbn13,
        ]);
    }

    private function home_recommendation(): array
    {
        $repository = $this->entityManager->getRepository(Book::class);

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

            $isbnArray[] = $book->getIsbn13();
        }

        return $isbnArray;
    }
}
