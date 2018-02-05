<?php

namespace App\Tests\Entity;

use App\Entity\User;
use \PHPUnit\Framework\TestCase;

/**
 * Unit tests for the User Entity Class
 * @author Christopher Bitler
 */
class UserTest extends TestCase
{
    const TEST_ID = 1;
    const TEST_USERNAME = 'test';
    const TEST_PASS = 'Not-A-Real-Hash';
    const TEST_IP = '127.0.0.1';
    const TEST_FIRST = 'FirstName';
    const TEST_LAST = 'LastName';
    const TEST_EMAIL = 'test@test.com';
    const TEST_ROLE = 1;

    public function testGetIdShouldReturnId()
    {
        $user = $this->generateTestUser();

        $this->assertEquals(self::TEST_ID,$user->getId());
    }

    public function testSetIdShouldChangeId()
    {
        $user = $this->generateTestUser();
        $id = $user->getId();
        $user->setId(2);

        $this->assertNotEquals($id,$user->getId());
    }

    public function testGetUsernameShouldReturnUsername()
    {
        $user = $this->generateTestUser();

        $this->assertEquals(self::TEST_USERNAME, $user->getUsername());
    }

    public function testSetUsernameShouldChangeUsername()
    {
        $user = $this->generateTestUser();
        $username = $user->getUsername();
        $user->setUsername('test123');

        $this->assertNotEquals($username, $user->getUsername());
    }

    public function testGetPasswordHashShouldReturnPasswordHash()
    {
        $user = $this->generateTestUser();

        $this->assertEquals(self::TEST_PASS, $user->getPasswordHash());
    }

    public function testSetPasswordHashShouldChangePasswordHash()
    {
        $user = $this->generateTestUser();
        $passwordHash = $user->getPasswordHash();
        $user->setPasswordHash('Not-A-Hash');

        $this->assertNotEquals($passwordHash, $user->getPasswordHash());
    }

    public function testGetLastIpShouldReturnLastIp()
    {
        $user = $this->generateTestUser();

        $this->assertEquals(self::TEST_IP, $user->getLastIp());
    }

    public function testSetLastIpShouldChangeLastIp()
    {
        $user = $this->generateTestUser();
        $lastIp = $user->getLastIp();
        $user->setLastIp('127.0.0.2');

        $this->assertNotEquals($lastIp, $user->getLastIp());
    }

    public function testGetFirstNameShouldReturnFirstName()
    {
        $user = $this->generateTestUser();

        $this->assertEquals(self::TEST_FIRST, $user->getFirstName());
    }

    public function testSetFirstNameShouldChangeFirstName()
    {
        $user = $this->generateTestUser();
        $firstName = $user->getFirstName();
        $user->setFirstName('Joe');

        $this->assertNotEquals($firstName, $user->getFirstName());
    }

    public function testGetLastNameShouldReturnLastName()
    {
        $user = $this->generateTestUser();

        $this->assertEquals(self::TEST_LAST, $user->getLastName());
    }

    public function testSetLastNameShouldChangeLastName()
    {
        $user = $this->generateTestUser();
        $lastName = $user->getLastName();
        $user->setLastName('Bob');

        $this->assertNotEquals($lastName, $user->getLastName());
    }

    public function testGetEmailShouldReturnEmail()
    {
        $user = $this->generateTestUser();

        $this->assertEquals(self::TEST_EMAIL, $user->getEmail());
    }

    public function testSetEmailShouldChangeEmail()
    {
        $user = $this->generateTestUser();
        $email = $user->getEmail();
        $user->setEmail('test@test2.com');

        $this->assertNotEquals($email, $user->getEmail());
    }

    public function testGetRoleShouldReturnRole()
    {
        $user = $this->generateTestUser();

        $this->assertEquals(self::TEST_ROLE, $user->getRole());
    }

    public function testSetRoleShouldChangeRole()
    {
        $user = $this->generateTestUser();
        $role = $user->getRole();
        $user->setRole(4);

        $this->assertNotEquals($role, $user->getRole());
    }

    private function generateTestUser() {
        $user = new User(self::TEST_USERNAME, self::TEST_PASS, self::TEST_IP, self::TEST_FIRST, self::TEST_LAST, self::TEST_EMAIL, self::TEST_ROLE);
        $user->setId(self::TEST_ID);
        return $user;
    }
}
