<?php

namespace App\Unit\Service;

use App\Entity\ScheduleItem;
use App\Service\ScheduleService;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the ScheduleService class
 * @author Christopher Bitler
 */
class ScheduleServiceTest extends TestCase
{
    /**
     * Note: Any tests relying on this time will break in 2038
     * This is because this is the max integer as far as epoch timestamps go
     */
    const MAX_TIME = 2147483647;

    const DATE_STRING = '2017-12-12T12:15';
    const FUTURE_DATE_STRING = '2037-12-12T12:15';

    const DESCRIPTION = 'Test';

    public function tearDown()
    {
        $this->addToAssertionCount(
            \Mockery::getContainer()->mockery_getExpectationCount()
        );
    }

    public function testAddItemToScheduleFailureShouldReturnInsertFailedInPast()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('getCurrentTimezone')->andReturn('America/New_York');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $scheduleService = new ScheduleService($manager, $repository, $timezoneService);

        $result = $scheduleService->addItemToSchedule(self::DESCRIPTION, self::DATE_STRING);

        $this->assertEquals(ScheduleService::INSERT_FAILED_IN_PAST, $result);
    }

    public function testAddItemToScheduleSuccessShouldReturnScheduleItem()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('getCurrentTimezone')->andReturn('America/New_York');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $manager->shouldReceive('persist')->once();
        $manager->shouldReceive('flush')->once();
        $scheduleService = new ScheduleService($manager, $repository, $timezoneService);

        $result = $scheduleService->addItemToSchedule(self::DESCRIPTION, self::FUTURE_DATE_STRING);

        $this->assertInstanceOf(ScheduleItem::class, $result);
    }

    public function testUpdateShouldCheckIfInPastInvalidIDShouldReturnError()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('getCurrentTimezone')->andReturn('America/New_York');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $repository->shouldReceive('findOneBy')->andReturn(null);
        $scheduleService = new ScheduleService($manager, $repository, $timezoneService);

        $result = $scheduleService->update(self::DESCRIPTION, self::DATE_STRING, 1);

        $this->assertEquals(ScheduleService::UPDATE_FAILED, $result);
    }

    public function testUpdateShouldCheckIfInPastTimeInPastShouldReturnError()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $scheduleItem = \Mockery::mock('App\Entity\ScheduleItem');
        $timezoneService->shouldReceive('getCurrentTimezone')->andReturn('America/New_York');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $repository->shouldReceive('findOneBy')->andReturn($scheduleItem);
        $scheduleService = new ScheduleService($manager, $repository, $timezoneService);

        $result = $scheduleService->update(self::DESCRIPTION, self::DATE_STRING, 1);

        $this->assertEquals(ScheduleService::UPDATE_FAILED, $result);
    }

    public function testUpdateShouldCheckIfInPastTimeInFutureShouldReturnScheduleItem()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $scheduleItem = \Mockery::mock('App\Entity\ScheduleItem');
        $timezoneService->shouldReceive('getCurrentTimezone')->andReturn('America/New_York');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $repository->shouldReceive('findOneBy')->andReturn($scheduleItem);
        $scheduleItem->shouldReceive('setDescription')->once();
        $scheduleItem->shouldReceive('setTimeAdded')->once();
        $scheduleItem->shouldReceive('setDateTime')->once();
        $scheduleItem->shouldReceive('setDateTimeString')->once();
        $manager->shouldReceive('flush')->once();
        $scheduleService = new ScheduleService($manager, $repository, $timezoneService);

        $result = $scheduleService->update(self::DESCRIPTION, self::FUTURE_DATE_STRING, 1);

        $this->assertInstanceOf(ScheduleItem::class, $result);
    }

    public function testUpdateShouldNotCheckIfInPastTimeInPastShouldReturnScheduleItem()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $scheduleItem = \Mockery::mock('App\Entity\ScheduleItem');
        $timezoneService->shouldReceive('getCurrentTimezone')->andReturn('America/New_York');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $repository->shouldReceive('findOneBy')->andReturn($scheduleItem);
        $scheduleItem->shouldReceive('setDescription')->once();
        $scheduleItem->shouldReceive('setTimeAdded')->once();
        $scheduleItem->shouldReceive('setDateTime')->once();
        $scheduleItem->shouldReceive('setDateTimeString')->once();
        $manager->shouldReceive('flush')->once();
        $scheduleService = new ScheduleService($manager, $repository, $timezoneService);

        $result = $scheduleService->update(self::DESCRIPTION, self::MAX_TIME, 1, false);

        $this->assertInstanceOf(ScheduleItem::class, $result);
    }

    public function testRefreshDateTimeStringsShouldUpdateItems()
    {
        $scheduleItems = array(
            \Mockery::mock('App\Entity\ScheduleItem'),
            \Mockery::mock('App\Entity\ScheduleItem'),
            \Mockery::mock('App\Entity\ScheduleItem'),
            \Mockery::mock('App\Entity\ScheduleItem')
        );

        $scheduleItem = \Mockery::mock('App\Entity\ScheduleItem');
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('getCurrentTimezone')->andReturn('America/New_York');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $repository->shouldReceive('findOneBy')->andReturn($scheduleItem);
        $repository->shouldReceive('findAll')->andReturn($scheduleItems);
        foreach ($scheduleItems as $si) {
            $si->shouldReceive('getDescription')->times(sizeof($scheduleItem))->andReturn(self::DESCRIPTION);
            $si->shouldReceive('getDateTime')->times(sizeof($scheduleItem))->andReturn(self::MAX_TIME);
            $si->shouldReceive('getId')->times(sizeof($scheduleItem))->andReturn(1);
        }
        $scheduleItem->shouldReceive('setDescription')->times(sizeof($scheduleItem));
        $scheduleItem->shouldReceive('setTimeAdded')->times(sizeof($scheduleItem));
        $scheduleItem->shouldReceive('setDateTime')->times(sizeof($scheduleItem));
        $scheduleItem->shouldReceive('setDateTimeString')->times(sizeof($scheduleItem));
        $manager->shouldReceive('flush')->times(sizeof($scheduleItem));
        $scheduleService = new ScheduleService($manager, $repository, $timezoneService);

        $scheduleService->refreshDateTimeStrings();
    }

    public function testRemoveItemFromScheduleItemDoesntExistShouldReturnError()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('getCurrentTimezone')->andReturn('America/New_York');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $repository->shouldReceive('findOneBy')->andReturn(null);
        $scheduleService = new ScheduleService($manager, $repository, $timezoneService);

        $result = $scheduleService->removeItemFromSchedule(1);

        $this->assertEquals(ScheduleService::REMOVE_NON_EXISTENT, $result);
    }

    public function testRemoveItemFromScheduleItemExistsShouldRemoveItem()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $scheduleItem = \Mockery::mock('App\Entity\ScheduleItem');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $timezoneService->shouldReceive('getCurrentTimezone')->andReturn('America/New_York');
        $timezoneService->shouldReceive('setScheduleService')->once();
        $repository->shouldReceive('findOneBy')->andReturn($scheduleItem);
        $manager->shouldReceive('remove')->once();
        $manager->shouldReceive('flush')->once();
        $scheduleService = new ScheduleService($manager, $repository, $timezoneService);

        $result = $scheduleService->removeItemFromSchedule(1);

        $this->assertEquals(ScheduleService::REMOVE_SUCCESS, $result);
    }

    public function testGetItemShouldReturnItemForId()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $scheduleItem = \Mockery::mock('App\Entity\ScheduleItem');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $repository->shouldReceive('findOneBy')->andReturn($scheduleItem);
        $timezoneService->shouldReceive('setScheduleService')->once();
        $scheduleService = new ScheduleService($manager, $repository, $timezoneService);

        $result = $scheduleService->getItem(1);

        $this->assertEquals($scheduleItem, $result);
    }

    public function testPollNewItemsShouldReturnArrayOfItems()
    {
        $scheduleItems = array(
            \Mockery::mock('App\Entity\ScheduleItem'),
            \Mockery::mock('App\Entity\ScheduleItem'),
            \Mockery::mock('App\Entity\ScheduleItem'),
            \Mockery::mock('App\Entity\ScheduleItem')
        );

        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $queryBuilder = \Mockery::mock('Doctrine\ORM\QueryBuilder');
        $expr = \Mockery::mock('Doctrine\ORM\Query\Expr');
        $query = \Mockery::mock('Docrine\ORM\Query');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $repository->shouldReceive('createQueryBuilder')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('where')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('expr')->andReturn($expr);
        $expr->shouldReceive('gt')->once();
        $queryBuilder->shouldReceive('getQuery')->andReturn($query);
        $query->shouldReceive('getArrayResult')->andReturn($scheduleItems);
        $timezoneService->shouldReceive('setScheduleService')->once();
        $scheduleService = new ScheduleService($manager, $repository, $timezoneService);

        $result = $scheduleService->pollNewItems(0);

        $this->assertEquals($scheduleItems, $result);
    }

    public function testGetScheduleItemsShouldReturnArrayOfItems()
    {
        $scheduleItems = array(
            \Mockery::mock('App\Entity\ScheduleItem'),
            \Mockery::mock('App\Entity\ScheduleItem'),
            \Mockery::mock('App\Entity\ScheduleItem'),
            \Mockery::mock('App\Entity\ScheduleItem')
        );

        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $timezoneService = \Mockery::mock('App\Service\TimezoneService');
        $repository->shouldReceive('findAll')->once()->andReturn($scheduleItems);
        $timezoneService->shouldReceive('setScheduleService')->once();
        $scheduleService = new ScheduleService($manager, $repository, $timezoneService);

        $result = $scheduleService->getScheduleItems();

        $this->assertEquals($scheduleItems, $result);
    }

}