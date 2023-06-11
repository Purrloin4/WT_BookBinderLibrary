<?php

namespace App\Tests\UnitTests;

use App\Entity\Message;
use PHPUnit\Framework\TestCase;
use App\Entity\User;

class MessageTest extends TestCase
{
    public function testSender(): void
    {
        $message = new Message();
        $user = new User();
        $message->setSender($user);
        $this->assertSame($user, $message->getSender());
    }

    public function testReceiver(): void
    {
        $message = new Message();
        $user = new User();
        $message->setReceiver($user);
        $this->assertSame($user, $message->getReceiver());
    }

    public function testMessage(): void
    {
        $message = new Message();
        $message->setMessage('message');
        $this->assertSame('message', $message->getMessage());
    }

    public function testTimestamp(): void
    {
        $message = new Message();
        $dateTimestamp = new \DateTime();
        $message->setTimestamp($dateTimestamp);
        $this->assertSame($dateTimestamp, $message->getTimestamp());
    }
}
