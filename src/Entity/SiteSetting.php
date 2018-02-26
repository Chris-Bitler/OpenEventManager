<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="settings")
 */
class SiteSetting
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string",name="`key`")
     * @var string
     */
    private $key;

    /**
     * @ORM\Column(type="string",name="`value`")
     * @var string
     */
    private $value;

    /**
     * Create a new Site setting object
     * @param string $key The key for the setting
     * @param string $value The value for the setting
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Get the key for the setting
     * @return string The key for the setting
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Set the key for the setting
     * @param string $key The string to set as the key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * Get the value of the setting
     * @return string The value of the setting
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set the value of the setting
     * @param string $value The value to set
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}