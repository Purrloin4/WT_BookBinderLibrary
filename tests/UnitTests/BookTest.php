<?php

namespace App\Tests\UnitTests;

use App\Entity\Book;
use App\Entity\Comment;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    public function testIsbn()
    {
        $book = new Book();
        $book->setIsbn('978-3-16-148410-0');
        $this->assertEquals('978-3-16-148410-0', $book->getIsbn());
    }

    public function testComments()
    {
        $book = new Book();
        $comment = new Comment();
        $book->addComment($comment);
        $this->assertContains($comment, $book->getComments());

        $book->removeComment($comment);
        $this->assertNotContains($comment, $book->getComments());
    }

    public function testPublishedDate()
    {
        $book = new Book();
        $book->setPublishedDate(new \DateTimeImmutable('2021-01-01'));
        $this->assertEquals(new \DateTimeImmutable('2021-01-01'), $book->getPublishedDate());
    }

    public function testRating()
    {
        $book = new Book();
        $book->setAverageRating(4.26);
        $this->assertEquals(4.26, $book->getAverageRating());

        $book->setRatingsCount(100);
        $this->assertEquals(100, $book->getRatingsCount());

        $book->addRating(5);
        $this->assertGreaterThan(4.26, $book->getAverageRating());
        $this->assertEquals(101, $book->getRatingsCount());

        $book->removeRating(5);
        $this->assertEquals(4.26, $book->getAverageRating());
        $this->assertEquals(100, $book->getRatingsCount());
    }
}
