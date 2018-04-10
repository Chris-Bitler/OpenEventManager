<?php

//TODO: Need to add security to endpoint
namespace App\API\V1\Admin;


use App\Service\ScheduleService;

/**
 * API for interacting with the schedule system
 * @author Christopher Bitler
 */
class Schedule
{
    /** @var ScheduleService */
    private $scheduleService;

    /**
     * Create a new Schedule API Object
     * @param ScheduleService $scheduleService Service for
     *          interacting with the scheduling system
     */
    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
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
        $result = $this->scheduleService->addItemToSchedule($description, $dateTime);
        if ($result == ScheduleService::INSERT_FAILED_IN_PAST) {
            return $this->generateReturnArray(true, 'You cannot insert an event in the past');
        }

        return $this->generateReturnArray(false, 'Schedule Item Added', array(
            'id' => $result->getId(),
            'dateTimeString' => $result->getDateTimeString(),
            'description' => $result->getDescription()
        ));
    }

    public function updateItem($id, $description, $eventDateTime)
    {
        $this->scheduleService->update($description, $eventDateTime, $id);
    }

    /**
     * Remove schedule item from the database
     * @param int $id The ID of the item to remove
     * @return array An array containing a boolean error value and message
     */
    public function removeItem($id)
    {
        $result = $this->scheduleService->removeItemFromSchedule($id);
        if ($result == ScheduleService::REMOVE_NON_EXISTENT) {
            return $this->generateReturnArray(true, 'Item does not exist.');
        }

        return $this->generateReturnArray(false, 'Item successfully removed.');
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