<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FriendshipFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $user1 = new User();
        $user1->setEmail($faker->email);
        $user1->setPassword($faker->password);
        $user1->setDisplayName($faker->name);

        $user2 = new User();
        $user2->setEmail($faker->email);
        $user2->setPassword($faker->password);
        $user2->setDisplayName($faker->name);

        $manager->flush();
    }
}
