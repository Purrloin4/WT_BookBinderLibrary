<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $user1 = new User();
        $user1->setEmail('hello1@world.org');
        $user1->setPassword($faker->password());
        $user1->setDisplayName($faker->name());

        $comment = new Comment();
        $comment->setMessage($faker->text());
        $comment->setCommenter($user1);

        $book = new Book();
        $book->setIsbn('9781338878929'); // faker will generate unused ISBNs
        $book->addComment($comment);

        $user2 = new User();
        $user2->setEmail('anotheruser@test.com');
        $user2->setPassword($faker->password());
        $user2->setDisplayName($faker->name());

        $manager->persist($book);
        $manager->persist($comment);
        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();


    }
}
