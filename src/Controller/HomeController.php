<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\FriendshipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_root')]
    #[Route('/home', name: 'app_home')]
    public function index(BookRepository $bookRepository, FriendshipRepository $friendshipRepository): Response
    {
        // Find sliding books
        $slidingBooks = $bookRepository->findTopRatedBooks(5);

        // Find popular books
        $popularBooks = $bookRepository->findPopularBooks(8);

        // Find books of the year
        $friendList = $friendshipRepository->findByUser($this->getUser());

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'sliding_books' => $slidingBooks,
            'popular_books' => $popularBooks,
            'friend_list' => $friendList,
        ]);
    }
}
