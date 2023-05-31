<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    #[Route('/user/{id}', name: 'user_profile')]
    public function show(User $user): Response
    {

        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }
}
