<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="schedule")
 * @author Christopher Bitler
 */
class ScheduleItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="bigint",name="timeAdded")
     * @var string
     */
    private $timeAdded;

    /**
     * @ORM\Column(type="string",name="description")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="bigint",name="`time`")
     * @var string
     */
    private $dateTime;

    /**
     * @ORM\Column(type="string",name="dateTimeString")
     * @var string
     */
    private $dateTimeString;

    /**
     * Create a new Schedule Item
     * @param string $timeAdded The time that the item was added
     * @param string $description The description of the item
     * @param string $dateTime The date/time for the schedule item
     * @param string $dateTimeString The friendly string representation of $dateTime
     */
    public function __construct($timeAdded, $description, $dateTime, $dateTimeString)
    {
        $this->timeAdded = $timeAdded;
        $this->description = $description;
        $this->dateTime = $dateTime;
        $this->dateTimeString = $dateTimeString;
    }

    /**
     * Get the ID
     * @return string The ID of the schedule item
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the ID
     * @param string $id The ID to set
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * Get the seconds since epoch for the time that the schedule item was added
     * @return string The seconds since epoch for the time that the schedule item was added
     */
    public function getTimeAdded(): string
    {
        return $this->timeAdded;
    }

    /**
     * Set the time that the schedule item was added
     * @param string $timeAdded The time to set for when the schedule item was added
     */
    public function setTimeAdded(string $timeAdded)
    {
        $this->timeAdded = $timeAdded;
    }

    /**
     * Get the description of the schedule item
     * @return string The description of the schedule item
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the description of the schedule item
     * @param string $description The description to set
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Get the number of seconds since epoch for the date/time of the event
     * @return string The number of seconds since the epoch for the date/time of the event
     */
    public function getDateTime(): string
    {
        return $this->dateTime;
    }

    /**
     * Set the number of seconds since the epoch for the date/time of the event
     * @param string $dateTime The number of seconds since the epoch for the date/time of the event
     */
    public function setDateTime(string $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     * Get the friendly date string representation of the associated date time
     * @return string The friendly date string representation
     */
    public function getDateTimeString(): string
    {
        return $this->dateTimeString;
    }

    /**
     * Set the friendly date string representation of the associated date time
     * @param string $dateTimeString The friendly date string to set
     */
    public function setDateTimeString(string $dateTimeString)
    {
        $this->dateTimeString = $dateTimeString;
    }
}
