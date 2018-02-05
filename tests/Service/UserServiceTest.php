<?php


namespace App\Tests\Service;


use App\Service\UserService;
use Doctrine\ORM\EntityRepository;
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
        /** @var EntityRepository|MockInterface */
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $userService = new UserService($repository, $manager);
    }
}
