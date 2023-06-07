<?php

namespace App\Tests\FunctionalTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{
    public function testHeaderLoads(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $responseContent = $client->getResponse()->getContent();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString('Book<span>Binder</span>', $responseContent);
    }

    public function testHomepageLoads(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $responseContent = $client->getResponse()->getContent();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString('Popular Books', $responseContent);
    }

    public function testLoginButton(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $link = $crawler->selectLink('Login')->link();
        $crawler = $client->click($link);

        $responseContent = $client->getResponse()->getContent();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString('Welcome!', $responseContent);
    }

    public function testRegisterButton(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $link = $crawler->selectLink('Register')->link();
        $crawler = $client->click($link);

        $responseContent = $client->getResponse()->getContent();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString('Register', $responseContent);
    }

    /*public function testProfileButton(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $link = $crawler->selectLink('Profile')->link();
        $crawler = $client->click($link);

        $responseContent = $client->getResponse()->getContent();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString('Profile', $responseContent);
    }*/

    public function testSlidingBooksLink(): void
    {
        // simulate clicking See The Book link
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $link = $crawler->selectLink('See The Book')->link();
        $crawler = $client->click($link);

        $responseContent = $client->getResponse()->getContent();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString('<h1 class="book_title">Book Title</h1>', $responseContent);
    }
}
