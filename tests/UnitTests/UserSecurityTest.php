<?php
namespace App\Tests\UnitTests;

use App\Security\User;
use PHPUnit\Framework\TestCase;

class UserSecurityTest extends TestCase
{
    public function testGetEmail()
    {
        $user = new User();
        $email = 'test@example.com';
        $user->setEmail($email);

        $this->assertEquals($email, $user->getEmail());
    }

    public function testGetUserIdentifier()
    {
        $user = new User();
        $email = 'test@example.com';
        $user->setEmail($email);

        $this->assertEquals($email, $user->getUserIdentifier());
    }

    public function testGetRoles()
    {
        $user = new User();
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $user->setRoles($roles);

        $expectedRoles = array_unique(array_merge($roles, ['ROLE_USER']));

        $this->assertEquals($expectedRoles, $user->getRoles());
    }
}
