<?php

namespace App\Controller;

use App\Entity\Friendship;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendshipController extends AbstractController
{
    #[Route('/friends', name: 'app_friends')]
    public function index(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $repo = $em->getRepository(Friendship::class);
        $user = $this->getUser();
        $pending = $repo->findBySender($user, false);
        $incoming = $repo->findByReceiver($user, false);
        $friends = $repo->findByUser($user);

        return $this->render('friendship/index.html.twig', [
            'controller_name' => 'FriendshipController',
            'pending' => $pending,
            'incoming' => $incoming,
            'friends' => array_map(function (Friendship $f) use ($user) {
                if ($f->getSender()->getId() === $user->getId()) {
                    return $f->getReceiver();
                } else {
                    return $f->getSender();
                }
            }, $friends),
        ]);
    }
}
