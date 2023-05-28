<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookDescriptionController extends AbstractController
{
    #[Route('/bookdescription', name: 'app_bookdescription')]
    public function viewBookDescription(): Response
    {
        return $this->render('bookdescription.html.twig', ['controller_name' => 'BookDescriptionController']);
    }
}
