<?php


namespace App\Tests\Unit\Entity;


use App\Entity\ScheduleItem;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the ScheduleItem class
 * @author Christopher Bitler
 */
class ScheduleItemTest extends TestCase
{
    /**
     * Generates a schedule item for testing
     * @return ScheduleItem The schedule item for testing
     */
    private function generateScheduleItem()
    {
        $scheduleItem = new ScheduleItem('0', 'Test', '1', '2012-12-12T12:12');
        return $scheduleItem;
    }

    public function testGetIdShouldReturnId()
    {
        $scheduleItem = $this->generateScheduleItem();
        $scheduleItem->setId("1");

        $this->assertEquals("1", $scheduleItem->getId());
    }

    public function testSetIdShouldUpdateId() {
        $scheduleItem = $this->generateScheduleItem();
        $start = $scheduleItem->getId();
        $scheduleItem->setId("2");
        $end = $scheduleItem->getId();

        $this->assertNotEquals($start, $end);
    }

    public function testGetTimeAddedShouldReturnTimeAdded()
    {
        $scheduleItem = $this->generateScheduleItem();

        $this->assertEquals('0', $scheduleItem->getTimeAdded());
    }

    public function testSetTimeAddedShouldUpdateTime() {
        $scheduleItem = $this->generateScheduleItem();
        $start = $scheduleItem->getTimeAdded();
        $scheduleItem->setTimeAdded('3');
        $end = $scheduleItem->getTimeAdded();

        $this->assertNotEquals($start, $end);
    }

    public function testDescriptionShouldReturnDescription()
    {
        $scheduleItem = $this->generateScheduleItem();

        $this->assertEquals('Test', $scheduleItem->getDescription());
    }

    public function testSetDescriptionShouldUpdateDescription() {
        $scheduleItem = $this->generateScheduleItem();
        $start = $scheduleItem->getDescription();
        $scheduleItem->setDescription('Test2');
        $end = $scheduleItem->getDescription();

        $this->assertNotEquals($start, $end);
    }

    public function testGetDateTimeShouldReturnDateTime()
    {
        $scheduleItem = $this->generateScheduleItem();

        $this->assertEquals('1', $scheduleItem->getDateTime());
    }

    public function testSetDateTimeShouldUpdateDateTime() {
        $scheduleItem = $this->generateScheduleItem();
        $start = $scheduleItem->getDateTime();
        $scheduleItem->setDateTime('3');
        $end = $scheduleItem->getDateTime();

        $this->assertNotEquals($start, $end);
    }

    public function testGetDateTimeStringShouldReturnDateTimeString()
    {
        $scheduleItem = $this->generateScheduleItem();

        $this->assertEquals("2012-12-12T12:12", $scheduleItem->getDateTimeString());
    }

    public function testSetDateTimeStringShouldUpdateDateTimeString() {
        $scheduleItem = $this->generateScheduleItem();
        $start = $scheduleItem->getDateTimeString();
        $scheduleItem->setDateTimeString('2012-12-12T12:13');
        $end = $scheduleItem->getDateTimeString();

        $this->assertNotEquals($start, $end);
    }

}