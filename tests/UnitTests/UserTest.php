<?php

namespace App\Tests\UnitTests;

use App\Entity\Comment;
use App\Entity\Friendship;
use App\Entity\Message;
use App\Entity\Subscribe;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testFriendships()
    {
        $user = new User();
        $friendship = new Friendship();
        $user->addFriendship($friendship);
        $this->assertContains($friendship, $user->getFriendships());

        $user->removeFriendship($friendship);
        $this->assertEmpty($user->getFriendships());
    }

    public function testMessages()
    {
        $user = new User();
        $user->setDisplayName('test');
        $message = new Message();
        $user->addSendMessage($message);
        $this->assertContains($message, $user->getSendMessages());

        $user->removeMessage($message);
        $this->assertEmpty($user->getSendMessages());

        $user->addReceivedMessage($message);
        $this->assertContains($message, $user->getReceivedMessages());

        $user->removeMessage($message);
        $this->assertEquals('This message was removed by: '.$user->getDisplayName(), $message->getMessage());
    }

    public function testSubcribe()
    {
        $user = new User();
        $subscribe = new Subscribe();
        $user->addSubscribe($subscribe);
        $this->assertContains($subscribe, $user->getSubscribes());

        $user->removeSubscribe($subscribe);
        $this->assertEmpty($user->getSubscribes());
    }
}
