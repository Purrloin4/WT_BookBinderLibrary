<?php

namespace App\Controller;

use App\Entity\Friendship;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/api/send_friend_request/{id}', name: 'app_api_send_friend_request', methods: ['POST'])]
    public function sendFriendRequest(EntityManagerInterface $em, int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $sender = $this->getUser();
        $receiver = $em->getRepository(User::class)->find($id);

        if (!$receiver) {
            return new JsonResponse([
                'status_code' => 404,
                'message' => "No user found for id $id",
            ]);
        }

        $friendship = new Friendship();
        $friendship->setSender($sender);
        $friendship->setReceiver($receiver);
        $friendship->setApproved(false);

        $em->persist($friendship);

        try {
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse([
                'status_code' => 500,
                'message' => "Woops! Something wrong with the database\n{$e->getMessage()}",
            ]);
        }

        return new JsonResponse([
            'status_code' => 200,
            'message' => "A friend request was successfully sent to {$receiver->getDisplayName()}!",
        ]);
    }

    #[Route('/api/approve_friend_request/{id}', name: 'app_api_approve_friend_request', methods: ['POST'])]
    public function approveFriendRequest(EntityManagerInterface $em, int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $friendship = $em->getRepository(Friendship::class)->find($id);

        if (!$friendship) {
            return new JsonResponse([
                'status_code' => 404,
                'message' => 'There is no such friend request',
            ]);
        }

        if ($friendship->isApproved()) {
            return new JsonResponse([
                'status_code' => 410,
                'message' => 'The friend request was already approved',
            ]);
        }

        $friendship->setApproved(true);
        $em->persist($friendship);

        try {
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse([
                'status_code' => 500,
                'message' => "Woops! Something wrong with the database\n{$e->getMessage()}",
            ]);
        }

        return new JsonResponse([
            'status_code' => 200,
            'message' => "A friend request was successfully approved. Now you are a friend with {$friendship->getSender()->getDisplayName()}!",
        ]);
    }
}
