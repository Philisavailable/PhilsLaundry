<?php

namespace App\Tests;

use App\DataFixtures\ServicesFixtures;
use App\Entity\Services;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ServicesControllerTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
       self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();

        // Load fixtures
        $this->loadFixtures([ServicesFixtures::class]);
    }

    private function loadFixtures(array $fixtures): void
    {
        $fixtureLoader = new Loader();
        foreach ($fixtures as $fixtureClass) {
            $fixtureLoader->addFixture(new $fixtureClass());
        }

        $executor = new ORMExecutor($this->entityManager, new ORMPurger());
        $executor->execute($fixtureLoader->getFixtures());
    }

    public function testServiceFixtureData(): void
    {
        $repository = $this->entityManager->getRepository(Services::class);
        $service = $repository->findOneBy(['title' => 'LavageTest']);
        
        $this->assertNotNull($service);
        $this->assertEquals('LavageTest', $service->getTitle());
        $this->assertEquals('image-lavage_test.jpg', $service->getPhoto());
        $this->assertEquals(15.00, $service->getPrice());
    }
}
