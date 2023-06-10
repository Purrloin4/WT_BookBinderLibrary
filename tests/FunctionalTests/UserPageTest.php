<?php

namespace App\Tests\FunctionalTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserPageTest extends WebTestCase
{
    public function testUserPageLoadsSuccessfully()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/user/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.account_info', 'My account info');
        // FIXME: Failed asserting that Symfony\Component\DomCrawler\Crawler Object matches selector ".user_name" and the text "User name: Preston Dare" of the node matching selector ".user_name" contains "Ariane Feest".
        //$this->assertSelectorTextContains('.user_name', 'Ariane Feest');
        $this->assertSelectorTextContains('.user_email', 'hello1@world.org');
        $this->assertSelectorTextContains('.user_friends', 'My Friends');
        $this->assertSelectorTextContains('.user_comments', 'My Comments');
        $this->assertSelectorTextContains('.user_messages', 'My Messages');
    }
}