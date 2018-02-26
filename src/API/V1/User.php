<?php

namespace App\API\V1;

use App\Service\SessionService;
use App\Service\UserService;

/**
 * API for creating, logging in, and checking data about users
 * @author Christopher Bitler
 */
class User
{
    /** @var UserService */
    private $userService;

    /** @var SessionService */
    private $sessionService;

    /**
     * Create a new instance of the User API
     * @param UserService|null $service The user service for interfacing with the database
     * @param SessionService|null $sessionService Service for generating a new session
     */
    public function __construct(
        UserService $service = null,
        SessionService $sessionService = null
    )
    {
        $this->userService = $service ?: new UserService();
        $this->sessionService = $sessionService ?: new SessionService();
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
        if (!$username || !$password || !$first || !$last || !$email || !$ip) {
            return $this->generateReturnArray(true, 'Please fill out the entire registration form');
        }
        $result = $this->userService->registerUser($username, $password, $first, $last, $email, $ip);
        if ($result == UserService::USER_CREATED) {
            return $this->generateReturnArray(false, 'User Registered');
        } elseif ($result == UserService::REGISTER_FAILED_TAKEN) {
            return $this->generateReturnArray(true, 'Username taken');
        } else {
            return $this->generateReturnArray(true, 'An unknown error has occurred during registration');
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
            $session = $this->sessionService->getNewSession();
            if(!$session->isStarted()) $session->start();
            $userFromDatabase = $this->userService->getUser($username);
            $user = array(
                'username' => $userFromDatabase->getUsername(),
                'role' => $userFromDatabase->getRole()
            );
            $session->set('user', $user);

            return $this->generateReturnArray(false, 'Login successful');
        } else if ($result == UserService::LOGIN_FAILED_NO_USER) {
            return $this->generateReturnArray(true, 'No such user exists');
        } else {
            return $this->generateReturnArray(true, 'Bad password');
        }
    }

    /**
     * Generate an array to be returned from the API
     * @param bool $error True if it is an error, false if not
     * @param string $message The message to return
     * @return array The generated values for returning from the API
     */
    private function generateReturnArray($error, $message)
    {
        return array(
            'error' => $error,
            'message' => $message
        );
    }
}
