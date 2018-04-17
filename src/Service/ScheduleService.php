<?php


namespace App\Service;

use App\Controller\ScheduleAdminController;
use App\Controller\ScheduleController;
use App\Utility\Database;
use DateTimeZone;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use App\Entity\ScheduleItem;

/**
 * Service for getting, querying, and updating the site's schedule
 * @package Service
 */
class ScheduleService
{
    const INSERT_SUCCESS = 1;
    const INSERT_FAILED_IN_PAST = 2;

    const REMOVE_NON_EXISTENT = 2;
    const REMOVE_SUCCESS = 1;

    const UPDATE_SUCCESS = 1;
    const UPDATE_FAILED = 2;

    const DATE_FORMAT = 'm-d-Y H:i T'; // Month/Day/Year Hour:Minute Timezone

    private $timezoneService;

    /**
     * Create a new SettingsService instance
     * @param EntityManager|null $entityManager The Doctrine Entity Manager
     * @param EntityRepository|null $repository The Doctrine Entity Repository
     * @param TimezoneService|null $timezoneService Timezone Service instance
     */
    public function __construct(
        EntityManager $entityManager = null,
        EntityRepository $repository = null,
        TimezoneService $timezoneService = null
    ) {
        try {
            $this->entityManager = $entityManager ?: (new Database())->createDoctrineObject();
        } catch (ORMException $e) {
            echo $e;
        }
        $this->repository = $repository ?: $this->entityManager->getRepository('App\Entity\ScheduleItem');
        $this->timezoneService = $timezoneService ?: new TimezoneService();
        $this->timezoneService->setScheduleService($this);
    }

    /**
     * Add an item to the schedule with a description and date/time for the event
     * @param string $description The description of the schedule item to add
     * @param string $eventDateTime The number of seconds since epoch for the date/time of the event
     * @return ScheduleItem|int New ScheduleItem if inserted, INSERT_FAILED_IN_PAST otherwise.
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addItemToSchedule($description, $eventDateTime)
    {
        $timeAdded = time();

        $timezone = new DateTimeZone($this->timezoneService->getCurrentTimezone());
        // This converts the html5 time to a unix timestamp
        $dateTime = \DateTime::createFromFormat(
            ScheduleAdminController::DATE_FORMAT,
            $eventDateTime,
            $timezone
        );

        $eventDateTimestamp = $dateTime->getTimestamp();
        $eventDateTimeString = $dateTime->format(self::DATE_FORMAT);

        if($eventDateTimestamp >= $timeAdded) {
            $scheduleItem = new ScheduleItem($timeAdded, $description, $eventDateTimestamp, $eventDateTimeString);
            $this->entityManager->persist($scheduleItem);
            $this->entityManager->flush();
            return $scheduleItem;
        } else {
            return self::INSERT_FAILED_IN_PAST;
        }
    }

    /**
     * Update the data for an item
     * @param string $description The new description for the item
     * @param string $eventDateTime The new date/time for the event
     * @param int $id The ID of the event
     * @param bool $checkInPast Whether or not to check if the time is in the past
     * @return ScheduleItem|int Updated Schedule item if successfully updated, otherwise UPDATE_FAILED
     */
    public function update($description, $eventDateTime, $id, $checkInPast = true)
    {
        $timeUpdated = time();

        $timezone = new DateTimeZone($this->timezoneService->getCurrentTimezone());
        // This converts the html5 time to a unix timestamp
        if($checkInPast) {
            $dateTime = \DateTime::createFromFormat(
                ScheduleAdminController::DATE_FORMAT,
                $eventDateTime,
                $timezone
            );
        } else {
            $dateTime = new \DateTime("@$eventDateTime");
            $dateTime->setTimezone($timezone);
        }

        $eventDateTimestamp = $dateTime->getTimestamp();
        $eventDateTimeString = $dateTime->format(self::DATE_FORMAT);

        if (($eventDateTimestamp > $timeUpdated && $checkInPast) || !$checkInPast) {
            $scheduleItem = $this->getItem($id);
            if ($scheduleItem != null) {
                $scheduleItem->setDescription($description);
                $scheduleItem->setTimeAdded($timeUpdated);
                $scheduleItem->setDateTime($eventDateTimestamp);
                $scheduleItem->setDateTimeString($eventDateTimeString);
                $this->entityManager->flush();

                return $scheduleItem;
            } else {
                return self::UPDATE_FAILED;
            }
        }

        return self::UPDATE_FAILED;
    }

    /**
     * This attempts to update the date time string for every schedule item via update() on Timezone change
     */
    public function refreshDateTimeStrings()
    {
        $items = $this->getScheduleItems();
        foreach ($items as $item) {
            $this->update($item->getDescription(), $item->getDateTime(), $item->getId(), false);
        }
    }

    /**
     * Remove an item from the schedle
     * @param int $id The ID of the schedule item to remove
     * @return int REMOVE_SUCCESS if the item is successfully removed, REMOVE_NON_EXISTENT otherwise
     */
    public function removeItemFromSchedule($id)
    {
        $scheduleItem = $this->repository->findOneBy(array(
            'id' => $id
        ));

        if ($scheduleItem === null) {
            return self::REMOVE_NON_EXISTENT;
        }

        $this->entityManager->remove($scheduleItem);
        $this->entityManager->flush();

        return self::REMOVE_SUCCESS;
    }

    /**
     * Get the item with a specific ID
     * @param int $id The ID of the item to retrieve
     * @return ScheduleItem|null
     */
    public function getItem($id)
    {
        $scheduleItem = $this->repository->findOneBy(array(
            'id' => $id
        ));

        return $scheduleItem;
    }

    /**
     * Get any new items that have appeared since a given epoch timestamp
     * @param string $timeLastRequest The time that the client last polled for new items
     * @return ScheduleItem[] The list of new items
     */
    public function pollNewItems($timeLastRequest)
    {
        $queryBuilder = $this->repository->createQueryBuilder('n');
        $scheduleItems = $queryBuilder->where(
            $queryBuilder->expr()->gt('timeAdded', $timeLastRequest)
        )->getQuery();

        return $scheduleItems->getArrayResult();
    }

    /**
     * Get all of the schedule items
     * @return ScheduleItem[] The list of all the schedule items
     */
    public function getScheduleItems()
    {
        $scheduleItems = $this->repository->findAll();
        return $scheduleItems;
    }
}
