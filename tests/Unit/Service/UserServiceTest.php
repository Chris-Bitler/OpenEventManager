<?php


namespace App\Tests\Unit\Service;


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
        $userService = new UserService($manager, $repository);

        $registered = $userService->registerUser(self::TEST_USERNAME, self::TEST_PASSWORD, self::TEST_FIRST, self::TEST_LAST, self::TEST_EMAIL, self::TEST_IP);

        $this->assertEquals(UserService::REGISTER_FAILED_TAKEN, $registered);
    }

    public function testRegisterUserWithValidUsernameShouldCatchORMException()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $repository->shouldReceive('findOneBy')->andReturn(null);
        $manager->shouldReceive('persist')->andThrow(new ORMException());
        $userService = new UserService($manager, $repository);

        $registered = $userService->registerUser(self::TEST_USERNAME, self::TEST_PASSWORD, self::TEST_FIRST, self::TEST_LAST, self::TEST_EMAIL, self::TEST_IP);

        $this->assertEquals(UserService::REGISTER_FAILED_ERROR, $registered);
    }

    public function testRegisterUserWithValidUsernameCreateUserShouldReturnUserCreated()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $repository->shouldReceive('findOneBy')->andReturn(null);
        $userService = new UserService($manager, $repository);
        $manager->shouldReceive('persist')->once();
        $manager->shouldReceive('flush')->once();
        $registered = $userService->registerUser(self::TEST_USERNAME, self::TEST_PASSWORD, self::TEST_FIRST, self::TEST_LAST, self::TEST_EMAIL, self::TEST_IP);

        $this->assertEquals(UserService::USER_CREATED, $registered);
    }

    public function testLoginInUserDoesNotExistShouldReturnFailedNoUser()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $repository->shouldReceive('findOneBy')->andReturn(null);
        $userService = new UserService($manager, $repository);

        $loginResult = $userService->loginUser(self::TEST_USERNAME, self::TEST_PASSWORD);

        $this->assertEquals(UserService::LOGIN_FAILED_NO_USER, $loginResult);
    }

    public function testLoginInBadPasswordShouldReturnFailedBadPassword()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $user = \Mockery::mock('App\Entity\User');
        $repository->shouldReceive('findOneBy')->andReturn($user);
        $user->shouldReceive('getPasswordHash')->andReturn("Bad-Password");
        $userService = new UserService($manager, $repository);

        $loginResult = $userService->loginUser(self::TEST_USERNAME, self::TEST_PASSWORD);

        $this->assertEquals(UserService::LOGIN_FAILED_BAD_PASSWORD, $loginResult);
    }

    public function testLoginInSuccessfulShouldReturnLoginSucces()
    {
        $password = '1234';
        $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $user = \Mockery::mock('App\Entity\User');
        $repository->shouldReceive('findOneBy')->andReturn($user);
        $user->shouldReceive('getPasswordHash')->andReturn($passwordHashed);
        $userService = new UserService($manager, $repository);

        $loginResult = $userService->loginUser(self::TEST_USERNAME, $password);

        $this->assertEquals(UserService::LOGIN_SUCCESS, $loginResult);
    }

    public function testGetUserNoSuchUserShouldReturnNull()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $repository->shouldReceive('findOneBy')->andReturn(null);
        $userService = new UserService($manager, $repository);

        $result = $userService->getUser(self::TEST_USERNAME);
        $this->assertNull($result);
    }

    public function testGetUserUserExistsShouldReturnUser()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $user = \Mockery::mock('App\Entity\User');
        $repository->shouldReceive('findOneBy')->andReturn($user);
        $userService = new UserService($manager, $repository);

        $result = $userService->getUser(self::TEST_USERNAME);
        $this->assertEquals($user, $result);
    }
}
