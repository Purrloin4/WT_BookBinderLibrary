<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_root')]
    #[Route('/home', name: 'app_home')]
    public function index(BookRepository $bookRepository, EntityManagerInterface $entityManager): Response
    {

        $popular_books = $bookRepository->findPopularBooks(8);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'popular_books' => $popular_books,
        ]);
    }
}
