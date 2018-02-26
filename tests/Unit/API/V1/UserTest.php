<?php


namespace App\Tests\Unit\API\V1;

use App\API\V1\User;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    const TEST_USERNAME = "Bob";
    const TEST_PASSWORD = "Not-A-Real-Password";
    const TEST_FIRST = "Bob";
    const TEST_LAST = "Joe";
    const TEST_EMAIL = "bob@email.com";
    const TEST_IP = "127.0.0.1";

    // Registration messages
    const EMPTY_FORM_ERROR = 'Please fill out the entire registration form';
    const TAKEN_ERROR = 'Username taken';
    const OTHER_ERROR = 'An unknown error has occurred during registration';
    const SUCCESS_MSG = 'User Registered';

    // Login messages
    const NO_SUCH_USER_ERROR = 'No such user exists';
    const BAD_PASSWORD_ERROR = 'Bad password';
    const LOGIN_SUCCESS = 'Login successful';

    public function testCheckUsernameAvailabilityUsernameNotAvailableShouldReturnFalse()
    {
        $userService = \Mockery::mock('App\Service\UserService');
        $user = \Mockery::mock('App\Entity\User');
        $userService->shouldReceive('getUser')->andReturn($user);
        $userApi = new User($userService);

        $available = $userApi->checkUsernameAvailability(self::TEST_USERNAME);
        $this->assertFalse($available);
    }

    public function testCheckUsernameAvailabilityUsernameAvailableShouldReturnTrue()
    {
        $userService = \Mockery::mock('App\Service\UserService');
        $userService->shouldReceive('getUser')->andReturn(null);
        $userApi = new User($userService);

        $available = $userApi->checkUsernameAvailability(self::TEST_USERNAME);
        $this->assertTrue($available);
    }

    public function testRegisterWithValuesMissingShouldReturnFillOutError()
    {
        $userService = \Mockery::mock('App\Service\UserService');
        $userApi = new User($userService);

        $result = $userApi->register(self::TEST_USERNAME, '', '', '', '', '');
        $this->assertTrue($result['error']);
        $this->assertEquals(self::EMPTY_FORM_ERROR, $result['message']);
    }

    public function testRegisterNameTakenShouldReturnNameTakenError()
    {
        $userService = \Mockery::mock('App\Service\UserService');
        $userService->shouldReceive('registerUser')->andReturn(UserService::REGISTER_FAILED_TAKEN);
        $userApi = new User($userService);

        $result = $userApi->register(self::TEST_USERNAME, self::TEST_PASSWORD, self::TEST_FIRST, self::TEST_LAST, self::TEST_EMAIL, self::TEST_IP);
        $this->assertTrue($result['error']);
        $this->assertEquals(self::TAKEN_ERROR, $result['message']);
    }

    public function testRegisterUnknownErrorShouldReturnUnknownError()
    {
        $userService = \Mockery::mock('App\Service\UserService');
        $userService->shouldReceive('registerUser')->andReturn(UserService::REGISTER_FAILED_ERROR);
        $userApi = new User($userService);

        $result = $userApi->register(self::TEST_USERNAME, self::TEST_PASSWORD, self::TEST_FIRST, self::TEST_LAST, self::TEST_EMAIL, self::TEST_IP);
        $this->assertTrue($result['error']);
        $this->assertEquals(self::OTHER_ERROR, $result['message']);
    }

    public function testRegisterSuccessShouldReturnSucessMessage()
    {
        $userService = \Mockery::mock('App\Service\UserService');
        $userService->shouldReceive('registerUser')->andReturn(UserService::LOGIN_SUCCESS);
        $userApi = new User($userService);

        $result = $userApi->register(self::TEST_USERNAME, self::TEST_PASSWORD, self::TEST_FIRST, self::TEST_LAST, self::TEST_EMAIL, self::TEST_IP);
        $this->assertFalse($result['error']);
        $this->assertEquals(self::SUCCESS_MSG, $result['message']);
    }

    public function testLoginNoSuchUserShouldReturnNoSuchUserMessage()
    {
        $userService = \Mockery::mock('App\Service\UserService');
        $userService->shouldReceive('loginUser')->andReturn(UserService::LOGIN_FAILED_NO_USER);
        $userApi = new User($userService);

        $result = $userApi->login(self::TEST_USERNAME, self::TEST_PASSWORD);
        $this->assertTrue($result['error']);
        $this->assertEquals(self::NO_SUCH_USER_ERROR, $result['message']);
    }

    public function testLoginBadPasswordShouldReturnBadPasswordMessage()
    {
        $userService = \Mockery::mock('App\Service\UserService');
        $userService->shouldReceive('loginUser')->andReturn(UserService::LOGIN_FAILED_BAD_PASSWORD);
        $userApi = new User($userService);

        $result = $userApi->login(self::TEST_USERNAME, self::TEST_PASSWORD);
        $this->assertTrue($result['error']);
        $this->assertEquals(self::BAD_PASSWORD_ERROR, $result['message']);
    }

    public function testLoginUserLoginSuccessShouldCreateSessionAndSetUsername()
    {
        $userService = \Mockery::mock('App\Service\UserService');
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $session = \Mockery::mock('Symfony\Component\HttpFoundation\Session\Session');
        $user = new \App\Entity\User("test","test","test","test","test","test",1);
        $userService->shouldReceive('loginUser')->andReturn(UserService::LOGIN_SUCCESS);
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(false);
        $session->shouldReceive('start')->once();
        $session->shouldReceive('set')->once();
        $userService->shouldReceive("getUser")->andReturn($user);
        $userApi = new User($userService, $sessionService);

        $result = $userApi->login(self::TEST_USERNAME, self::TEST_PASSWORD);
        $this->assertFalse($result['error']);
        $this->assertEquals(self::LOGIN_SUCCESS, $result['message']);
    }

    /**
     * TODO: Add Login Success test.. Need a better way to unit test session creation
     */
}
