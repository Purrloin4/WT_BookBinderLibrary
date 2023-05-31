<?php

namespace App\Controller;

use App\Form\UserSettingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserSettingController extends AbstractController
{
    #[Route('/settings', name: 'user_settings')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserSettingType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'User settings have been updated successfully.');

            return $this->redirectToRoute('user_settings');
        }

        return $this->render('user/setting.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
