<?php

namespace App\Tests\Unit\Service;

use App\Entity\SiteSetting;
use App\Service\SettingsService;
use PHPUnit\Framework\TestCase;

class SettingsServiceTest extends TestCase
{
    public function tearDown()
    {
        $this->addToAssertionCount(
            \Mockery::getContainer()->mockery_getExpectationCount()
        );
    }

    public function testGetSettingWithFakeSettingShouldReturnNull()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $settingsService = new SettingsService($manager, $repository);
        $repository->shouldReceive('findOneBy')->andReturn(null);

        $setting = $settingsService->getSetting('fake-setting');

        $this->assertEquals(null, $setting);
    }

    public function testGetSettingWithRealSettingShouldReturnSetting()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $settingsService = new SettingsService($manager, $repository);
        $settingObject = new SiteSetting('real-setting', 'value');
        $repository->shouldReceive('findOneBy')->andReturn($settingObject);

        $setting = $settingsService->getSetting('real-setting');

        $this->assertEquals('value', $setting);
    }

    public function testGetSettingObjectWithFakeSettingShouldReturnNull()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $settingsService = new SettingsService($manager, $repository);
        $repository->shouldReceive('findOneBy')->andReturn(null);

        $setting = $settingsService->getSettingObject('fake-setting');

        $this->assertEquals(null, $setting);
    }

    public function testGetSettingObjectWithRealSettingShouldReturnSettingObject()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $settingsService = new SettingsService($manager, $repository);
        $settingObject = new SiteSetting('real-setting', 'value');
        $repository->shouldReceive('findOneBy')->andReturn($settingObject);

        $setting = $settingsService->getSettingObject('real-setting');

        $this->assertEquals($settingObject, $setting);
    }

    public function testInsertSettingShouldAttemptToInsertSetting()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $manager->shouldReceive('persist')->once();
        $manager->shouldReceive('flush')->once();
        $settingsService = new SettingsService($manager, $repository);

        $settingsService->insertSetting('test', 'test');
    }

    public function testUpdateSettingSettingDoesntExistShouldThrowException()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $repository->shouldReceive('findOneBy')->andReturn(null);
        $manager->shouldReceive('persist')->once();
        $manager->shouldReceive('flush')->once();
        $settingService = new SettingsService($manager, $repository);

        $settingService->updateSetting('test', 'test');
    }

    public function testUpdateSettingSettingEistsShouldUpdateSetting()
    {
        $repository = \Mockery::mock('Doctrine\ORM\EntityRepository');
        $manager = \Mockery::mock('Doctrine\ORM\EntityManager');
        $settingObject = new SiteSetting('test','test2');
        $repository->shouldReceive('findOneBy')->andReturn($settingObject);
        $manager->shouldReceive('flush');
        $settingService = new SettingsService($manager, $repository);

        $settingService->updateSetting('test', 'test');

        $this->assertNotEquals('test2', $settingObject->getValue());
    }
}