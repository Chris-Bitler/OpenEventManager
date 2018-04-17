<?php

namespace App\API\V1\Admin;

use App\Service\ScheduleService;
use App\Service\SessionService;
use App\Service\TimezoneService;

/**
 * API for interacting with the schedule system
 * @author Christopher Bitler
 */
class Schedule
{
    const NOT_AUTHORIZED = 'Not authorized to access this endpoint';
    const EVENT_IN_PAST = 'You cannot insert an event in the past';
    const ITEM_ADDED = 'Schedule Item Added';
    const ITEM_NOT_EXIST = 'Item does not exist';
    const ITEM_REMOVED = 'Item successfully removed';
    const UPDATE_FAILED = 'Updating schedule item failed';
    const UPDATE_SUCCESS = 'Schedule item updated';
    const INVALID_TIMEZONE = 'Invalid timezone selected';
    const TIMEZONE_UPDATED = 'Timezone updated';

    /** @var ScheduleService */
    private $scheduleService;

    /** @var SessionService */
    private $sessionService;

    /** @var TimezoneService */
    private $timezoneService;

    /**
     * Create a new Schedule API Object
     * @param ScheduleService|null $scheduleService Service for
     *          interacting with the scheduling system
     * @param SessionService|null $sessionService Service for getting user sessions
     * @param TimezoneService|null $timezoneService Service for updating timezone
     */
    public function __construct(
        ScheduleService $scheduleService = null,
        SessionService $sessionService = null,
        TimezoneService $timezoneService = null
    ) {
        $this->scheduleService = $scheduleService ?: new ScheduleService();
        $this->sessionService = $sessionService ?: new SessionService();
        $this->timezoneService = $timezoneService ?: new TimezoneService();
        $this->timezoneService->setScheduleService($this->scheduleService);
    }

    /**
     * Get a list of schedule items
     * @return array Array containing subarrays with the description,
     *              date/time, and id of the item
     */
    public function getAllItems()
    {
        $data = [];
        $scheduleData = $this->scheduleService->getScheduleItems();
        foreach ($scheduleData as $item) {
            $data[] = [
                'description' => $item->getDescription(),
                'dateTime' => $item->getDateTime(),
                'id' => $item->getId()
            ];
        }

        return $data;
    }

    /**
     * Get a list of schedule items containing new items added since a epoch timestamp
     * @return array Array containing subarrays with the description,
     *              date/time, and id of the item
     */
    public function getNewItems($lastRequestTime)
    {
        $data = [];
        $scheduleData = $this->scheduleService->pollNewItems($lastRequestTime);
        foreach ($scheduleData as $item) {
            $data[] = [
                'description' => $item->getDescription(),
                'dateTime' => $item->getDateTime(),
                'id' => $item->getId()
            ];
        }

        return $data;
    }

    /**
     * Add a new schedule item to the database
     * @param string $description The description for the event
     * @param string $dateTime The date/time for the event in seconds since epoch
     * @return array An array containing a boolean error value and message
     */
    public function addItem($description, $dateTime)
    {
        $session = $this->sessionService->getNewSession();
        if (!$session->isStarted()) $session->start();
        $user = $session->get('user');
        if ($user && $user['role'] == 5) {
            $result = $this->scheduleService->addItemToSchedule($description, $dateTime);
            if (is_int($result)  && $result == ScheduleService::INSERT_FAILED_IN_PAST) {
                return $this->generateReturnArray(true, self::EVENT_IN_PAST);
            }

            return $this->generateReturnArray(false, self::ITEM_ADDED, array(
                'id' => $result->getId(),
                'dateTimeString' => $result->getDateTimeString(),
                'description' => $result->getDescription()
            ));
        } else {
            return $this->generateReturnArray(true, self::NOT_AUTHORIZED);
        }
    }

    /**
     * Update a schedule item
     * @param string $id Id of the item
     * @param string $description The new description
     * @param string $eventDateTime The new date/time
     * @return array Array containing an error value and a message
     */
    public function updateItem($id, $description, $eventDateTime)
    {
        $session = $this->sessionService->getNewSession();
        if (!$session->isStarted()) $session->start();
        $user = $session->get('user');
        if ($user && $user['role'] == 5) {
            $result = $this->scheduleService->update($description, $eventDateTime, $id);
            if(is_int($result) && $result == ScheduleService::UPDATE_FAILED) {
                return $this->generateReturnArray(true, self::UPDATE_FAILED);
            } else {
                return $this->generateReturnArray(false, self::UPDATE_SUCCESS, array(
                    'dateTimeString' => $result->getDateTimeString(),
                    'description' => $result->getDescription(),
                    'id' => $result->getId()
                ));
            }
        } else {
            return $this->generateReturnArray(true, self::NOT_AUTHORIZED);
        }
    }

    /**
     * Remove schedule item from the database
     * @param int $id The ID of the item to remove
     * @return array An array containing a boolean error value and message
     */
    public function removeItem($id)
    {
        $session = $this->sessionService->getNewSession();
        if (!$session->isStarted()) $session->start();
        $user = $session->get('user');
        if ($user && $user['role'] == 5) {
            $result = $this->scheduleService->removeItemFromSchedule($id);
            if ($result == ScheduleService::REMOVE_NON_EXISTENT) {
                return $this->generateReturnArray(true, self::ITEM_NOT_EXIST);
            }

            return $this->generateReturnArray(false, self::ITEM_REMOVED);
        } else {
            return $this->generateReturnArray(true, self::NOT_AUTHORIZED);
        }
    }

    /**
     * Update the site timezone
     * @param string $timezone The timezone to set
     * @return array Array containing boolean error value and message
     */
    public function updateTimezone($timezone) {
        $session = $this->sessionService->getNewSession();
        if (!$session->isStarted()) $session->start();
        $user = $session->get('user');
        if ($user && $user['role'] == 5) {
            if ($this->timezoneService->isValidTimezone($timezone)) {
                $this->timezoneService->setTimezone(
                    $this->timezoneService->getPHPTimezone($timezone)
                );

                return $this->generateReturnArray(false, self::TIMEZONE_UPDATED);
            } else {
                return $this->generateReturnArray(true, self::INVALID_TIMEZONE);
            }
        } else {
            return $this->generateReturnArray(true, self::NOT_AUTHORIZED);
        }
    }

    /**
     * Generate an array to be returned from the API
     * @param bool $error True if it is an error, false if not
     * @param string $message The message to return
     * @param array $params Miscellaneous parameters
     * @return array The generated values for returning from the API
     */
    private function generateReturnArray($error, $message, $params = null)
    {
        return array(
            'error' => $error,
            'message' => $message,
            'params' => $params
        );
    }
}
