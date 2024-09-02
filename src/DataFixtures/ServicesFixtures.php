<?php

namespace App\DataFixtures;

use App\Entity\Services;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ServicesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $services = new Services();
        $services->setTitle('LavageTest');
        $services->setPhoto('image-lavage_test.jpg');
        $services->setPrice(15.00);
        $services->setDescription('Description for LavageTest');
        $services->setCreatedAt(new DateTime());
        $services->setUpdatedAt(new DateTime());
        $services->setDeletedAt(null);

        $manager->persist($services);
        $manager->flush();
    }
}
