<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 2/8/2018
 * Time: 8:47 AM
 */

namespace App\API\V1;

use App\Service\UserService;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * API for creating, logging in, and checking data about users
 * @author Christopher Bitler
 */
class User
{
    /** @var UserService */
    private $userService;

    /**
     * Create a new instance of the User API
     * @param UserService|null $service The user service for interfacing with the database
     */
    public function __construct(UserService $service = null)
    {
        $this->userService = $service ?: new UserService();
    }

    /**
     * Check if a username is available
     * @param string $username The username to check
     * @return bool true if the username is free, false otherwise
     */
    public function checkUsernameAvailability($username)
    {
        $user = $this->userService->getUser($username);
        return $user == null;
    }

    /**
     * Register a user in the database
     * @param string $username The username to register
     * @param string $password The password to register for the user
     * @param string $first The first name for the user
     * @param string $last The last name for the user
     * @param string $email The email for the user
     * @param string $ip The ip for the user
     * @return array Array containing a value 'error' stating whether or not it was a valid result
     *  and a value 'message' containing any message related to the response to the request
     */
    public function register($username, $password, $first, $last, $email, $ip)
    {
        $result = $this->userService->registerUser($username, $password, $first, $last, $email, $ip);
        if ($result == UserService::USER_CREATED) {
            return array(
                'error' => false,
                'message' => 'User Registered'
            );
        } elseif ($result == UserService::REGISTER_FAILED_TAKEN) {
            return array(
                'error' => true,
                'message' => 'Username taken'
            );
        } else {
            return array(
                'error' => true,
                'message' => 'An unknown error has occured during registration'
            );
        }
    }

    /**
     * Attempt to log in a user
     * Note: This uses symfony's session class to show that the user was logged in
     * @param string $username The username to use for logging in
     * @param string $password The password to use for logging in
     * @return array Array containing a value 'error' stating whether or not it was a valid result
     *  and a value 'message' containing any message related to the response to the request
     */
    public function login($username, $password)
    {
        $result = $this->userService->loginUser($username, $password);
        if ($result == UserService::LOGIN_SUCCESS) {
            $session = new Session();
            if(!$session->isStarted()) $session->start();
            $session->set('username', $username);

            return array(
                'error' => false,
                'message' => 'Login successful'
            );
        } else if ($result == UserService::LOGIN_FAILED_NO_USER) {
            return array(
                'error' => true,
                'message' => 'No such user exists'
            );
        } else {
            return array(
                'error' => true,
                'message' => 'Bad password'
            );
        }
    }
}
