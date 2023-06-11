<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Comment;
use App\Entity\Subscribe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadSubscribes($manager);
        $this->loadComments($manager);

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager): void
    {
        $faker = Factory::create('en_EN');

        // Set a specific set of cities
        $cities = ['Brussels', 'Antwerp', 'Ghent', 'Bruges', 'Liège', 'Namur', 'Leuven', 'Mons', 'Mechelen'];

        for ($i = 0; $i < 10; ++$i) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPassword($faker->password());
            $user->setDisplayName($faker->name());

            // Set a random birthday between 18 and 65 years ago
            $birthday = $faker->dateTimeBetween('-22 years', '-14 years');
            $user->setBirthday($birthday);

            // Set a random gender (Male or Female)
            $gender = $faker->randomElement(['Male', 'Female']);
            $user->setGender($gender);

            // Set a random city from the defined set
            $city = $faker->randomElement($cities);
            $user->setCity($city);

            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }
    }

    private function loadSubscribes(ObjectManager $manager): void
    {
        $faker = Factory::create('en_EN');

        $users = $manager->getRepository(User::class)->findAll();
        $books = $manager->getRepository(Book::class)->findAll();

        foreach ($books as $book) {
            // Randomly choose a user to subscribe
            $user = $faker->randomElement($users);
            $subscribe = new Subscribe();
            $subscribe->setUser($user);
            $subscribe->setBook($book);
            $subscribe->setTimeStamp($faker->dateTimeBetween('-1 year', 'now'));

            $manager->persist($subscribe);
        }
    }

    private function loadComments(ObjectManager $manager): void
    {
        $faker = Factory::create('en_EN');

        $users = $manager->getRepository(User::class)->findAll();
        $books = $manager->getRepository(Book::class)->findAll();

        foreach ($books as $book) {
            // Randomly choose a user to comment
            $user = $faker->randomElement($users);
            $comment = new Comment();
            $comment->setCommenter($user);
            $comment->setBook($book);
            $comment->setMessage($faker->text());
            $comment->setTimeStamp($faker->dateTimeBetween('-1 year', 'now'));

            $manager->persist($comment);
        }
    }
}
