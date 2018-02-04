<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * Handle user registration, log in, permission checking, etc
 * @author Christopher Bitler
 */
class UserService
{
    /** @var EntityRepository */
    private $repository;

    /** @var EntityManager */
    private $entityManager;

    public function __construct(
        EntityRepository $repository,
        EntityManager $entityManager
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function registerUser($username, $password, $first, $last, $ip) {
        $password = password_hash($password, PASSWORD_DEFAULT);

        //TODO: Check for already existing user.
        $existingUser = $this->repository->findOneBy(array(
           'username' => $username
        ));

        if($existingUser == null) {
            $user = new User($username, $password, $ip, $first, $last, 1);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } else {

        }
    }
}