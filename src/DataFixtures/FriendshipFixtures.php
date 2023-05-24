<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FriendshipFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $user1 = new User();
        $user1->setEmail('hello@world.org');
        $user1->setPassword($faker->password());
        $user1->setDisplayName($faker->name());

        $user2 = new User();
        $user2->setEmail('foo@bar.org');
        $user2->setPassword($faker->password());
        $user2->setDisplayName($faker->name());

        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();
    }
}
