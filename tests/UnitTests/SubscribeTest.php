<?php

namespace App\Tests\UnitTests;

use App\Entity\Book;
use App\Entity\Subscribe;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class SubscribeTest extends TestCase
{
    public function testUser()
    {
        $subscribe = new Subscribe();
        $user = new User();
        $subscribe->setUser($user);
        $this->assertEquals($user, $subscribe->getUser());
    }

    public function testBook()
    {
        $subscribe = new Subscribe();
        $book = new Book();
        $subscribe->setBook($book);
        $this->assertEquals($book, $subscribe->getBook());
    }
}
