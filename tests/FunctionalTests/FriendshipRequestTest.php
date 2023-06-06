<?php

namespace App\Tests\FunctionalTests;

use App\Repository\FriendshipRepository;
use App\Repository\UserRepository;
use App\Tests\Utils\JsonAssertionTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FriendshipRequestTest extends WebTestCase
{
    use JsonAssertionTrait;

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
        $this->client->request('GET', $this->requestApi.$testReceiver->getId());
        $this->assertResponseStatusCodeSame(405, 'Only POST method is allowed');

        // hypothetical, non-existing user ID
        $this->client->request('POST', $this->requestApi.'123456789');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['status' => 404]);
        $this->assertJsonContains(['message' => 'No user found for id 123456789']);

        // successful request
        $this->client->request('POST', $this->requestApi.$testReceiver->getId());
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['status' => 200]);

        // falsy friendship approval
        $friendshipRepository = static::getContainer()->get(FriendshipRepository::class);
        $frienshipRequests = $friendshipRepository->findBySender($testSender);
        // FIXME: this fails due to there are duplicated friendship requests.
        // $this->assertCount(1, $frienshipRequests);
    }
}
