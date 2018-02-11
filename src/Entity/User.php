<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $passwordHash;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $lastIp;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $firstName;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $lastName;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     * @var int
     */
    private $role;

    /**
     * User constructor.
     * @param $username string The user's username
     * @param $passwordHash string The bcrypt hash of the user's password
     * @param $lastIp string The last IP the user used
     * @param $firstName string The user's first name
     * @param $lastName string The user's last name
     * @param $email string The user's email
     * @param $role int The user's role number
     */
    public function __construct($username, $passwordHash, $lastIp, $firstName, $lastName, $email, $role)
    {
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->lastIp = $lastIp;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->role = $role;
    }

    /**
     * Get the User's ID
     * @return string The user's ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the User's ID
     * @param string $id The ID to set
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the user's username
     * @return string The user's username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the user's username
     * @param string $username The username to set
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get the user's password hash
     * @return string The user's password hash
     */
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    /**
     * Set the user's password hash
     * @param string $passwordHash The password hash to set
     */
    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * Get the user's last IP
     * @return string The user's last IP
     */
    public function getLastIp()
    {
        return $this->lastIp;
    }

    /**
     * Set the user's last IP
     * @param string $lastIp The IP to set
     */
    public function setLastIp($lastIp)
    {
        $this->lastIp = $lastIp;
    }

    /**
     * Get the user's first name
     * @return string The user's first name
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the user's first name
     * @param string $firstName The first name to set
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Get the user's last name
     * @return string The user's last name
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the user's last name
     * @param string $lastName The last name to set
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Get the user's email
     * @return string The user's email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the user's email
     * @param string $email The email to set
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * Get the user's role number
     * @return int The user's role number
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set the user's role number
     * @param string $role The role number to set
     */
    public function setRole($role)
    {
        $this->role = $role;
    }
}
