<?php

namespace App\Tests\FunctionalTests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentsTest extends WebTestCase
{
    public function testAddComment(): void
    {
        $client = static::createClient();
        // Simulate logging in a user
        $userRepository = $client->getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => 'hello1@world.org']);
        $client->loginUser($user);
        $crawler = $client->request('GET', '/book/1');

        $form = $crawler->selectButton('Submit')->form();
        $form->setValues([
            'comment_message_form[message]' => 'This is a test comment',
        ]);
        $client->submit($form);

        $responseContent = $client->getResponse()->getContent();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString('This is a test comment', $responseContent);
    }

    public function testEditComment(): void
    {
        $client = static::createClient();
        // Simulate logging in a user
        $userRepository = $client->getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => 'hello1@world.org']);
        $client->loginUser($user);
        $crawler = $client->request('GET', '/book/1');

        // Simulate clicking the edit link of a comment
        $editLink = $crawler->filterXPath('//a[contains(text(), "Edit")]')->first()->link();
        $crawler = $client->click($editLink);

        // Simulate editing the comment
        $form = $crawler->selectButton('Submit')->form();
        $form->setValues([
            'comment_message_form[message]' => 'This is an edited comment',
        ]);
        $client->submit($form);
        $crawler = $client->followRedirect();

        $responseContent = $client->getResponse()->getContent();
        $this->assertStringContainsString('This is an edited comment', $responseContent);
    }

    public function testDeleteComment(): void
    {
        $client = static::createClient();
        // Simulate logging in a user
        $userRepository = $client->getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => 'hello1@world.org']);
        $client->loginUser($user);
        $crawler = $client->request('GET', '/book/1');

        // Simulate clicking the delete link of a comment
        $deleteLink = $crawler->filterXPath('//a[contains(text(), "Delete")]')->first()->link();
        $client->click($deleteLink);

        $crawler = $client->followRedirect();
        // Assert that the comment was successfully deleted
        $responseContent = $client->getResponse()->getContent();
        $this->assertStringNotContainsString('This is an edited comment', $responseContent);
    }
}
