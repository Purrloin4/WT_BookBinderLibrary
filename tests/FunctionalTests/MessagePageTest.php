<?php

namespace App\Tests\FunctionalTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessagePageTest extends WebTestCase
{
    public function testMessagePageLoadsSuccessfully()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/messages');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Recent chats');
    }
}
