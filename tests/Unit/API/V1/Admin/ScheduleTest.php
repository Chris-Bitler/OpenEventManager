<?php

namespace Unit\API\V1\Admin;

use App\API\V1\Admin\Schedule;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Schedule API
 * @author Christopher Bitler
 */
class ScheduleTest extends TestCase
{
    public function testGetAllItemsShouldReturnArrayOfScheduleItems() {
        /** @var array[][] */
        $items = array(
            array(
                'description' => 'Test',
                'dateTime' => '12345',
                'id' => 1
            ),
            array(
                'description' => 'Test',
                'dateTime' => '12345',
                'id' => 1
            ),
            array(
                'description' => 'Test',
                'dateTime' => '12345',
                'id' => 1
            )
        );
        $itemObjects = array(
            \Mockery::mock('App\Entity\ScheduleItem'),
            \Mockery::mock('App\Entity\ScheduleItem'),
            \Mockery::mock('App\Entity\ScheduleItem')
        );
        $scheduleService = \Mockery::mock('App\Service\ScheduleService');
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('setScheduleService')->once();
        foreach ($itemObjects as $item) {
            $item->shouldReceive('getDescription')->once()->andReturn('Test');
            $item->shouldReceive('getDateTime')->once()->andReturn('12345');
            $item->shouldReceive('getId')->once()->andReturn(1);
        }
        $scheduleService->shouldReceive('getScheduleItems')->andReturn($itemObjects);
        $schedule = new Schedule($scheduleService, $sessionService, $timezoneService);
        $result = $schedule->getAllItems();

        $this->assertEquals($items, $result);
    }
}