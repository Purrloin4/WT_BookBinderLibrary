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
        $friendshipRequests = $friendshipRepository->findBySender($testSender);

        $badActor = $userRepository->findOneByEmail('hello1@world.org');
        $this->client->loginUser($badActor);
        $this->client->request('POST', '/api/approve_friend_request/'.$friendshipRequests[0]->getId());
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['status' => 403]);

        // falsy friendship approval with wrong ID
        $this->client->request('POST', '/api/approve_friend_request/123456789');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['status' => 404]);

        // successful friendship approval
        $this->client->loginUser($testReceiver);
        $this->client->request('POST', '/api/approve_friend_request/'.$friendshipRequests[0]->getId());
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['status' => 200]);

        $this->assertNotNull($friendshipRepository->findOneBy(['sender' => $testSender, 'receiver' => $testReceiver]));

        // double approval
        $this->client->request('POST', '/api/approve_friend_request/'.$friendshipRequests[0]->getId());
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['status' => 410]);

        // friendship removal
        $this->client->request('POST', '/api/remove_friend/'.$friendshipRequests[0]->getId());
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['status' => 200]);
        $this->assertNull($friendshipRepository->findOneBy(['sender' => $testSender, 'receiver' => $testReceiver]));

        // friendship removal with wrong ID
        $this->client->request('POST', '/api/remove_friend/123456789');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['status' => 404]);

        // unauthorized friendship removal
        $this->client->loginUser($testSender);
        $this->client->request('POST', $this->requestApi.$testReceiver->getId());

        $friendshipRequests = $friendshipRepository->findBySender($testSender);

        $this->client->loginUser($badActor);
        $this->client->request('POST', '/api/remove_friend/'.$friendshipRequests[0]->getId());
        $this->assertResponseIsSuccessful();
        $this->assertContains($friendshipRequests[0], $friendshipRepository->findBySender($testSender));

        // reject friendship request
        $this->client->loginUser($testSender);
        $this->client->request('POST', $this->requestApi.$testReceiver->getId());

        $this->client->loginUser($testReceiver);
        $friendshipRequests = $friendshipRepository->findByReceiver($testReceiver);
        $this->client->request('POST', '/api/reject_friend_request/'.$friendshipRequests[0]->getId());
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['status' => 200]);

        // reject friendship request with wrong ID
        $this->client->request('POST', '/api/reject_friend_request/123456789');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['status' => 404]);

        // unauthorized friendship rejection
        $this->client->loginUser($testSender);
        $this->client->request('POST', $this->requestApi.$testReceiver->getId());

        $friendshipRequests = $friendshipRepository->findBySender($testSender);
        $this->client->loginUser($badActor);
        $this->client->request('POST', '/api/reject_friend_request/'.$friendshipRequests[0]->getId());
        $this->assertResponseIsSuccessful();
        $this->assertContains($friendshipRequests[0], $friendshipRepository->findBySender($testSender));
    }
}
