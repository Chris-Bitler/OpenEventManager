<?php

namespace Unit\API\V1\Admin;

use App\API\V1\Admin\Schedule;
use App\Service\ScheduleService;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Schedule API
 * @author Christopher Bitler
 */
class ScheduleTest extends TestCase
{
    public function tearDown()
    {
        $this->addToAssertionCount(
            \Mockery::getContainer()->mockery_getExpectationCount()
        );
    }

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
            $item->shouldReceive('getDescription')->andReturn('Test');
            $item->shouldReceive('getDateTime')->andReturn('12345');
            $item->shouldReceive('getId')->andReturn(1);
        }
        $scheduleService->shouldReceive('getScheduleItems')->andReturn($itemObjects);
        $schedule = new Schedule($scheduleService, $sessionService, $timezoneService);
        $result = $schedule->getAllItems();

        $this->assertEquals($items, $result);
    }

    public function testGetNewItemsShouldReturnArrayOfScheduleItems() {
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
            $item->shouldReceive('getDescription')->andReturn('Test');
            $item->shouldReceive('getDateTime')->andReturn('12345');
            $item->shouldReceive('getId')->andReturn(1);
        }
        $scheduleService->shouldReceive('pollNewItems')->andReturn($itemObjects);
        $schedule = new Schedule($scheduleService, $sessionService, $timezoneService);
        $result = $schedule->getNewItems(0);

        $this->assertEquals($items, $result);
    }

    public function testAddItemNoPermissionsShouldReturnNotAuthorized()
    {
        $scheduleService = \Mockery::mock('App\Service\ScheduleService');
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $session = \Mockery::mock('\Symfony\Component\HttpFoundation\Session\Session');
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(false);
        $session->shouldReceive('start')->once();
        $session->shouldReceive('get')->andReturn(array(
            'role' => 1
        ));
        $schedule = new Schedule($scheduleService, $sessionService, $timezoneService);

        $result = $schedule->addItem('Test', 'Test');

        $this->assertEquals(array(
            'error' => true,
            'message' => Schedule::NOT_AUTHORIZED,
            'params' => null
        ), $result);
    }

    public function testAddItemInPastShouldReturnEventInPast()
    {
        $scheduleService = \Mockery::mock('App\Service\ScheduleService');
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $session = \Mockery::mock('\Symfony\Component\HttpFoundation\Session\Session');
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(true);
        $session->shouldReceive('get')->andReturn(array(
            'role' => 5
        ));
        $scheduleService
            ->shouldReceive('addItemToSchedule')->andReturn(ScheduleService::INSERT_FAILED_IN_PAST);
        $schedule = new Schedule($scheduleService, $sessionService, $timezoneService);

        $result = $schedule->addItem('Test', 'Test');

        $this->assertEquals(array(
            'error' => true,
            'message' => Schedule::EVENT_IN_PAST,
            'params' => null
        ), $result);
    }

    public function testAddItemSuccessShouldReturnItem()
    {
        $scheduleService = \Mockery::mock('App\Service\ScheduleService');
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $session = \Mockery::mock('\Symfony\Component\HttpFoundation\Session\Session');
        $scheduleItem = \Mockery::mock('App\Entity\ScheduleItem');
        $scheduleItem->shouldReceive('getId')->andReturn('1');
        $scheduleItem->shouldReceive('getDateTimeString')->andReturn('12-12-12 12:12');
        $scheduleItem->shouldReceive('getDescription')->andReturn('Test');
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(true);
        $session->shouldReceive('get')->andReturn(array(
            'role' => 5
        ));
        $scheduleService
            ->shouldReceive('addItemToSchedule')->andReturn($scheduleItem);
        $schedule = new Schedule($scheduleService, $sessionService, $timezoneService);

        $result = $schedule->addItem('Test', 'Test');

        $this->assertEquals(array(
            'error' => false,
            'message' => Schedule::ITEM_ADDED,
            'params' => array(
                'id' => '1',
                'dateTimeString' => '12-12-12 12:12',
                'description' => 'Test'
            )
        ), $result);
    }

    public function testUpdateItemSuccessShouldReturnItem()
    {
        $scheduleService = \Mockery::mock('App\Service\ScheduleService');
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $session = \Mockery::mock('\Symfony\Component\HttpFoundation\Session\Session');
        $scheduleItem = \Mockery::mock('App\Entity\ScheduleItem');
        $scheduleItem->shouldReceive('getId')->andReturn('1');
        $scheduleItem->shouldReceive('getDateTimeString')->andReturn('12-12-12 12:12');
        $scheduleItem->shouldReceive('getDescription')->andReturn('Test');
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(true);
        $session->shouldReceive('get')->andReturn(array(
            'role' => 5
        ));
        $scheduleService
            ->shouldReceive('update')->andReturn($scheduleItem);
        $schedule = new Schedule($scheduleService, $sessionService, $timezoneService);

        $result = $schedule->updateItem('1', 'Test', 'Test');

        $this->assertEquals(array(
            'error' => false,
            'message' => Schedule::UPDATE_SUCCESS,
            'params' => array(
                'id' => '1',
                'dateTimeString' => '12-12-12 12:12',
                'description' => 'Test'
            )
        ), $result);
    }

    public function testUpdateItemNoPermissionsShouldReturnNotAuthorized()
    {
        $scheduleService = \Mockery::mock('App\Service\ScheduleService');
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $session = \Mockery::mock('\Symfony\Component\HttpFoundation\Session\Session');
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(false);
        $session->shouldReceive('start')->once();
        $session->shouldReceive('get')->andReturn(array(
            'role' => 1
        ));
        $schedule = new Schedule($scheduleService, $sessionService, $timezoneService);

        $result = $schedule->updateItem('1','Test', 'Test');

        $this->assertEquals(array(
            'error' => true,
            'message' => Schedule::NOT_AUTHORIZED,
            'params' => null
        ), $result);
    }

    public function testUpdateFailedShouldReturnUpdateFailed()
    {
        $scheduleService = \Mockery::mock('App\Service\ScheduleService');
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $session = \Mockery::mock('\Symfony\Component\HttpFoundation\Session\Session');
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(true);
        $session->shouldReceive('get')->andReturn(array(
            'role' => 5
        ));
        $scheduleService
            ->shouldReceive('update')->andReturn(ScheduleService::UPDATE_FAILED);
        $schedule = new Schedule($scheduleService, $sessionService, $timezoneService);

        $result = $schedule->updateItem('1','Test', 'Test');

        $this->assertEquals(array(
            'error' => true,
            'message' => Schedule::UPDATE_FAILED,
            'params' => null
        ), $result);
    }

    public function testRemoveItemNoPermissionsShouldReturnNotAuthorized()
    {
        $scheduleService = \Mockery::mock('App\Service\ScheduleService');
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $session = \Mockery::mock('\Symfony\Component\HttpFoundation\Session\Session');
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(false);
        $session->shouldReceive('start')->once();
        $session->shouldReceive('get')->andReturn(array(
            'role' => 1
        ));
        $schedule = new Schedule($scheduleService, $sessionService, $timezoneService);

        $result = $schedule->removeItem('1');

        $this->assertEquals(array(
            'error' => true,
            'message' => Schedule::NOT_AUTHORIZED,
            'params' => null
        ), $result);
    }

    public function testRemoveItemNonExistentShouldReturnItemNotExist()
    {
        $scheduleService = \Mockery::mock('App\Service\ScheduleService');
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $session = \Mockery::mock('\Symfony\Component\HttpFoundation\Session\Session');
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(true);
        $session->shouldReceive('get')->andReturn(array(
            'role' => 5
        ));
        $scheduleService
            ->shouldReceive('removeItemFromSchedule')->andReturn(ScheduleService::REMOVE_NON_EXISTENT);
        $schedule = new Schedule($scheduleService, $sessionService, $timezoneService);

        $result = $schedule->removeItem('1');

        $this->assertEquals(array(
            'error' => true,
            'message' => Schedule::ITEM_NOT_EXIST,
            'params' => null
        ), $result);
    }

    public function testRemoveItemSuccessShouldReturnItemRemoved()
    {
        $scheduleService = \Mockery::mock('App\Service\ScheduleService');
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $session = \Mockery::mock('\Symfony\Component\HttpFoundation\Session\Session');
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(true);
        $session->shouldReceive('get')->andReturn(array(
            'role' => 5
        ));
        $scheduleService
            ->shouldReceive('removeItemFromSchedule')->once();
        $schedule = new Schedule($scheduleService, $sessionService, $timezoneService);

        $result = $schedule->removeItem('1');

        $this->assertEquals(array(
            'error' => false,
            'message' => Schedule::ITEM_REMOVED,
            'params' => null
        ), $result);
    }

    public function testUpdateTimezoneNotAuthorizedShouldReturnNotAuthorized()
    {
        $scheduleService = \Mockery::mock('App\Service\ScheduleService');
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $session = \Mockery::mock('\Symfony\Component\HttpFoundation\Session\Session');
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(true);
        $session->shouldReceive('get')->andReturn(array(
            'role' => 1
        ));
        $schedule = new Schedule($scheduleService, $sessionService, $timezoneService);

        $result = $schedule->updateTimezone('America/New_York');

        $this->assertEquals(array(
            'error' => true,
            'message' => Schedule::NOT_AUTHORIZED,
            'params' => null
        ), $result);
    }

    public function testUpdateTimezoneInvalidTimezoneShouldReturnInvalidTimezone()
    {
        $scheduleService = \Mockery::mock('App\Service\ScheduleService');
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $session = \Mockery::mock('\Symfony\Component\HttpFoundation\Session\Session');
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(true);
        $session->shouldReceive('get')->andReturn(array(
            'role' => 5
        ));
        $timezoneService->shouldReceive('isValidTimezone')->andReturn(false);
        $schedule = new Schedule($scheduleService, $sessionService, $timezoneService);

        $result = $schedule->updateTimezone('Fake-Timezone');

        $this->assertEquals(array(
            'error' => true,
            'message' => Schedule::INVALID_TIMEZONE,
            'params' => null
        ), $result);
    }

    public function testUpdateTimezoneValidTimezoneShouldReturnTimezoneUpdated()
    {
        $scheduleService = \Mockery::mock('App\Service\ScheduleService');
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $session = \Mockery::mock('\Symfony\Component\HttpFoundation\Session\Session');
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(true);
        $session->shouldReceive('get')->andReturn(array(
            'role' => 5
        ));
        $timezoneService->shouldReceive('isValidTimezone')->andReturn(true);
        $timezoneService->shouldReceive('setTimezone')->once();
        $timezoneService->shouldReceive('getPHPTimezone')->andReturn('America/New_York');
        $schedule = new Schedule($scheduleService, $sessionService, $timezoneService);

        $result = $schedule->updateTimezone('EST');

        $this->assertEquals(array(
            'error' => false,
            'message' => Schedule::TIMEZONE_UPDATED,
            'params' => null
        ), $result);
    }
}