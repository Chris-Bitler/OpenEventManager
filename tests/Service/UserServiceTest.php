<?php


namespace App\Tests\Service;


use App\Service\UserService;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    const TEST_USERNAME = "Bob";
    const TEST_PASSWORD = "Not-A-Real-Password";
    const TEST_FIRST = "Bob";
    const TEST_LAST = "Joe";
    const TEST_EMAIL = "bob@email.com";
    const TEST_IP = "127.0.0.1";

    public function testRegisterUserWithTakenUsernameShouldReturnRegisterFailedTaken()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $user = \Mockery::mock('App\Entity\User');
        $repository->shouldReceive('findOneBy')->andReturn($user);
        $userService = new UserService($repository, $manager);

        $registered = $userService->registerUser(self::TEST_USERNAME, self::TEST_PASSWORD, self::TEST_FIRST, self::TEST_LAST, self::TEST_EMAIL, self::TEST_IP);

        $this->assertEquals(UserService::REGISTER_FAILED_TAKEN, $registered);
    }

    public function testRegisterUserWithValidUsernameShouldCatchORMException()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $repository->shouldReceive('findOneBy')->andReturn(null);
        $manager->shouldReceive('persist')->andThrow(new ORMException());
        $userService = new UserService($repository, $manager);

        $registered = $userService->registerUser(self::TEST_USERNAME, self::TEST_PASSWORD, self::TEST_FIRST, self::TEST_LAST, self::TEST_EMAIL, self::TEST_IP);

        $this->assertEquals(UserService::REGISTER_FAILED_ERROR, $registered);
    }

    public function testRegisterUserWithValidUsernameCreateUserShouldReturnUserCreated()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $repository->shouldReceive('findOneBy')->andReturn(null);
        $userService = new UserService($repository, $manager);

        $registered = $userService->registerUser(self::TEST_USERNAME, self::TEST_PASSWORD, self::TEST_FIRST, self::TEST_LAST, self::TEST_EMAIL, self::TEST_IP);

        $this->assertEquals(UserService::REGISTER_FAILED_ERROR, $registered);
    }
}
