<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FriendshipRequestTest extends WebTestCase
{
    private $requestApi = '/api/send_friend_request/';
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testApiReturns404WithAnnonymousUser(): void
    {
        $response = $this->client->request('POST', $this->requestApi.'1');
        $this->assertResponseStatusCodeSame(302, 'Annonymouse user should not have access to the API');
    }

    public function testApiWithUser1(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testSender = $userRepository->findOneByEmail('hello@world.org');
        $testReceiver = $userRepository->findOneByEmail('foo@bar.org');
        $this->client->loginUser($testSender);
        $response = $this->client->request('POST', $this->requestApi.$testReceiver->getId());
        $this->assertResponseIsSuccessful();
        // TODO: Check the JSON content!
    }
}
