<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_root')]
    #[Route('/home', name: 'app_home')]
    public function index(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        // FIXME: Find how to pick the sliding books
        $sliding_books = array_slice($books, 0, 5, true);

        // FIXME: Find how to pick the popular books
        $popular_books = array_slice($books, 5, 8, true);

        // FIXME: Find how to pick the books of the year
        $year_books = array_slice($books, 13, 5, true);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'sliding_books' => $sliding_books,
            'popular_books' => $popular_books,
            'year_books' => $year_books,
        ]);
    }
}