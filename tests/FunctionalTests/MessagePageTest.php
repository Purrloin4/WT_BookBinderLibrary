<?php

namespace App\Tests\FunctionalTests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessagePageTest extends WebTestCase
{
    public function testMessagePageLoadsSuccessfully()
    {
        $client = static::createClient();

        // Simulate logging in a different user
        $userRepository = $client->getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => 'hello@world.org']);
        $client->loginUser($user);
        $crawler = $client->request('GET', '/messages');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Recent chats');
    }
}
