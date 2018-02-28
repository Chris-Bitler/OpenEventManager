<?php

namespace App\Service;

use App\Entity\User;
use App\Utility\Database;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;

/**
 * Handle user registration, log in, permission checking, etc
 * @author Christopher Bitler
 */
class UserService
{
    // Constants related to user registration
    const USER_CREATED = 1;
    const REGISTER_FAILED_TAKEN = 2;
    const REGISTER_FAILED_ERROR = 3;

    //Constants related to login
    const LOGIN_SUCCESS = 1;
    const LOGIN_FAILED_BAD_PASSWORD = 2;
    const LOGIN_FAILED_NO_USER = 3;

    /** @var EntityRepository */
    private $repository;

    /** @var EntityManager */
    private $entityManager;

    public function __construct(
        EntityManager $entityManager = null,
        EntityRepository $repository = null
    )
    {
        try {
            $this->entityManager = $entityManager ?: (new Database())->createDoctrineObject();
        } catch (ORMException $e) {
            echo $e;
        }
        $this->repository = $repository ?: $this->entityManager->getRepository('App\Entity\User');
    }

    /**
     * Register a user in the database
     * @param string $username The user's username
     * @param string $password The user's password
     * @param string $first The user's first name
     * @param string $last The user's last name
     * @param string $email The user's email
     * @param string $ip The user's ip
     * @return int USER_CREATED if user is created, or either of the REGISTER_FAILED constants if the user was not created
     */
    public function registerUser($username, $password, $first, $last, $email, $ip)
    {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $existingUser = $this->repository->findOneBy(array(
           'username' => $username
        ));

        if($existingUser == null) {
            $user = new User($username, $password, $ip, $first, $last, $email, 1);
            try {
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                return self::USER_CREATED;
            } catch (ORMException $e) {
                return self::REGISTER_FAILED_ERROR;
            }
        } else {
            return self::REGISTER_FAILED_TAKEN;
        }
    }

    /**
     * Get a user object based on the username
     * @param string $username The user's name
     * @return User|null The user object if the user exists, null otherwise
     */
    public function getUser($username)
    {
        $user = $this->repository->findOneBy(
            array(
                'username' => $username
            )
        );

        return $user;
    }

    /**
     * Check to see if the user's provided password matches their recorded password
     * @param string $username The user's username
     * @param string $password The password that the user entered.
     * @return int LOGIN_SUCCESS if successfully logged in, otherwise one of the LOGIN_FAILED values depending on the
     *      condition
     */
    public function loginUser($username, $password)
    {
        /** @var $existingUser User */
        $existingUser = $this->repository->findOneBy(array(
            'username' => $username
        ));

        if ($existingUser) {
            if (password_verify($password, $existingUser->getPasswordHash())) {
                return self::LOGIN_SUCCESS;
            } else {
                return self::LOGIN_FAILED_BAD_PASSWORD;
            }
        } else {
            return self::LOGIN_FAILED_NO_USER;
        }
    }
}
