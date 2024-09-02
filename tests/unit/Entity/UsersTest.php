<?php

namespace App\Tests\Entity;

use App\Entity\Users;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UsersTest extends TestCase
{
    private Users $Users;

    protected function setUp(): void
    {
        $this->Users = new Users();
        $this->Users->setEmail('test@example.com');
        $this->Users->setFirstName('John');
        $this->Users->setName('Doe');
        $this->Users->setPassword('hashed_password');
        $this->Users->setCreatedAt(new DateTime());
    }

    public function testInitialValues()
    {
        $this->assertNull($this->Users->getId());
        $this->assertEquals('test@example.com', $this->Users->getEmail());
        $this->assertEquals('John', $this->Users->getFirstName());
        $this->assertEquals('Doe', $this->Users->getName());
        $this->assertInstanceOf(DateTime::class, $this->Users->getCreatedAt());
    }

    public function testSettersAndGetters()
    {
        $this->Users->setEmail('newemail@example.com');
        $this->Users->setFirstName('Jane');
        $this->Users->setName('Smith');
        $this->Users->setPassword('new_hashed_password');
        $this->Users->setCreatedAt(new DateTime('2023-01-01'));

        $this->assertEquals('newemail@example.com', $this->Users->getEmail());
        $this->assertEquals('Jane', $this->Users->getFirstName());
        $this->assertEquals('Smith', $this->Users->getName());
        $this->assertEquals('new_hashed_password', $this->Users->getPassword());
        $this->assertEquals(new DateTimeImmutable('2023-01-01'), $this->Users->getCreatedAt());
    }

    public function testGetRoles()
    {
        $this->assertEquals(['ROLE_USER'], $this->Users->getRoles());

        $this->Users->setRoles(['ROLE_ADMIN']);
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $this->Users->getRoles());
    }

    
    public function testUsersIdentifier()
    {
        $this->assertEquals('test@example.com', $this->Users->getEmail());
    }

    public function testEraseCredentials()
    {
        $this->Users->eraseCredentials();
        $this->assertTrue(true); 
    }
}