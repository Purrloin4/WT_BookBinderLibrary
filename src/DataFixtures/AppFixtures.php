<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadBooks($manager);

        $manager->flush();
    }

    private function loadBooks(ObjectManager $manager): void
    {
        // Big Magic / Elizabeth Gilbert
        $b01 = new Book();
        $b01->setIsbn('978-1594634727');
        $manager->persist($b01);

        // Ten Thousand Skies Above You / Claudia Gray
        $b02 = new Book();
        $b02->setIsbn('978-0062279002');
        $manager->persist($b02);

        // A Tale For The Time Being / Ruth Ozeki
        $b03 = new Book();
        $b03->setIsbn('978-0143124870');
        $manager->persist($b03);

        // The Great Gatsby / F.Scott Fitzgerald
        $b04 = new Book();
        $b04->setIsbn('978-0743273565');
        $manager->persist($b04);

        // After You / Jojo Moyes
        $b05 = new Book();
        $b05->setIsbn('978-0143131397');
        $manager->persist($b05);

        // Changes Are / Richard Russo
        $b06 = new Book();
        $b06->setIsbn('978-1101971994');
        $manager->persist($b06);

        // Dominicana / Angie Cruz
        $b07 = new Book();
        $b07->setIsbn('978-1250205933');
        $manager->persist($b07);

        // The Travellers / Regina Porter
        $b08 = new Book();
        $b08->setIsbn('978-0525576198');
        $manager->persist($b08);

        // Afternoon Of A Faun / James Lasdun
        $b09 = new Book();
        $b09->setIsbn('978-0393357882');
        $manager->persist($b09);

        // Flash Count Diary / Darcey Steinke
        $b10 = new Book();
        $b10->setIsbn('978-0374156114');
        $manager->persist($b10);

        // Picnic Comma Lightning / Laurence Scott
        $b11 = new Book();
        $b11->setIsbn('978-0393609974');
        $manager->persist($b11);

        // Very Nice / Marcy Dermansky
        $b12 = new Book();
        $b12->setIsbn('978-0525655633');
        $manager->persist($b12);

        // Stay and Fight / Madeline ffitch
        $b13 = new Book();
        $b13->setIsbn('978-1250619556');
        $manager->persist($b13);

        // Disappearing Earth / Julia Phillips
        $b14 = new Book();
        $b14->setIsbn('978-0525520412');
        $manager->persist($b14);

        // Lost Children Archive / Valeria Luiselli
        $b15 = new Book();
        $b15->setIsbn('978-0525520610');
        $manager->persist($b15);

        // Phantoms: A Thriller / Dean Koontz
        $b16 = new Book();
        $b16->setIsbn('978-0425253748');
        $manager->persist($b16);

        // Midnight in Chernobyl / Adam Higginbotham
        $b17 = new Book();
        $b17->setIsbn('978-1501134616');
        $manager->persist($b17);

        // 10 Minutes 38 Seconds / Elif Shafak
        $b18 = new Book();
        $b18->setIsbn('978-0241293867');
        $manager->persist($b18);
    }
}
