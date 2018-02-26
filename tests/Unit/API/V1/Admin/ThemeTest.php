<?php

namespace App\Tests\Unit\API\V1\Admin;

use App\API\V1\Admin\Theme;
use PHPUnit\Framework\TestCase;

class ThemeTest extends TestCase
{
    const ROLE = 5;
    const CURRENT_THEME_COLOR = "#FFFFFF";
    const CURRENT_TEXT_COLOR = 0;
    const NEW_THEME_COLOR = "#010101";
    const NEW_TEXT_COLOR = 255;
    const CURRENT_NAME = "Test";
    const NEW_NAME = "Test2";

    public function testUpdateColorInvalidRoleShouldReturnNotAuthorized()
    {
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $settingsService = \Mockery::mock('App\Service\SettingsService');
        $session = \Mockery::mock('Symfony\Component\HttpFoundation\Session\Session');
        $sessionArray = array('username' => 'test', 'role' => 1);
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(false);
        $session->shouldReceive('start')->once();
        $session->shouldReceive('get')->andReturn($sessionArray);

        $theme = new Theme($settingsService, $sessionService);
        $response = $theme->updateColor(self::NEW_THEME_COLOR, self::NEW_TEXT_COLOR);

        $this->assertTrue($response['error']);
        $this->assertEquals(Theme::NOT_AUTHORIZED, $response['message']);
    }

    public function testUpdateColorNoPreviousThemeAndTextColorShouldInsert()
    {
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $settingsService = \Mockery::mock('App\Service\SettingsService');
        $session = \Mockery::mock('Symfony\Component\HttpFoundation\Session\Session');
        $sessionArray = array('username' => 'test', 'role' => 5);
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(false);
        $session->shouldReceive('start')->once();
        $session->shouldReceive('get')->andReturn($sessionArray);
        $settingsService->shouldReceive('getSetting')
            ->withArgs(array('theme.color'))->andReturn(null);
        $settingsService->shouldReceive('getSetting')
            ->withArgs(array('theme.textColor'))->andReturn(null);
        $settingsService->shouldReceive('insertSetting')
            ->withArgs(array('theme.color', self::NEW_THEME_COLOR))->once();
        $settingsService->shouldReceive('insertSetting')
            ->withArgs(array('theme.textColor', self::NEW_TEXT_COLOR))->once();

        $theme = new Theme($settingsService, $sessionService);
        $response = $theme->updateColor(self::NEW_THEME_COLOR, self::NEW_TEXT_COLOR);

        $this->assertFalse($response['error']);
        $this->assertEquals(Theme::THEME_COLOR_UPDATED, $response['message']);
    }

    public function testUpdateColorPreviousColorAndTextColorShouldUpdate()
    {
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $settingsService = \Mockery::mock('App\Service\SettingsService');
        $session = \Mockery::mock('Symfony\Component\HttpFoundation\Session\Session');
        $sessionArray = array('username' => 'test', 'role' => 5);
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(false);
        $session->shouldReceive('start')->once();
        $session->shouldReceive('get')->andReturn($sessionArray);
        $settingsService->shouldReceive('getSetting')
            ->withArgs(array('theme.color'))->andReturn(self::CURRENT_THEME_COLOR);
        $settingsService->shouldReceive('getSetting')
            ->withArgs(array('theme.textColor'))->andReturn(self::CURRENT_TEXT_COLOR);
        $settingsService->shouldReceive('updateSetting')
            ->withArgs(array('theme.color', self::NEW_THEME_COLOR))->once();
        $settingsService->shouldReceive('updateSetting')
            ->withArgs(array('theme.textColor', self::NEW_TEXT_COLOR))->once();

        $theme = new Theme($settingsService, $sessionService);
        $response = $theme->updateColor(self::NEW_THEME_COLOR, self::NEW_TEXT_COLOR);

        $this->assertFalse($response['error']);
        $this->assertEquals(Theme::THEME_COLOR_UPDATED, $response['message']);
    }

    public function testUpdateNameInvalidRoleShouldReturnNotAuthorized()
    {
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $settingsService = \Mockery::mock('App\Service\SettingsService');
        $session = \Mockery::mock('Symfony\Component\HttpFoundation\Session\Session');
        $sessionArray = array('username' => 'test', 'role' => 1);
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(false);
        $session->shouldReceive('start')->once();
        $session->shouldReceive('get')->andReturn($sessionArray);

        $theme = new Theme($settingsService, $sessionService);
        $response = $theme->updateColor(self::NEW_THEME_COLOR, self::NEW_TEXT_COLOR);

        $this->assertTrue($response['error']);
        $this->assertEquals(Theme::NOT_AUTHORIZED, $response['message']);
    }

    public function testUpdateNameNoPreviousNameShouldInsertName()
    {
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $settingsService = \Mockery::mock('App\Service\SettingsService');
        $session = \Mockery::mock('Symfony\Component\HttpFoundation\Session\Session');
        $sessionArray = array('username' => 'test', 'role' => 5);
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(false);
        $session->shouldReceive('start')->once();
        $session->shouldReceive('get')->andReturn($sessionArray);
        $settingsService->shouldReceive('getSetting')
            ->withArgs(array('site.name'))->andReturn(null);
        $settingsService->shouldReceive('insertSetting')
            ->withArgs(array('site.name', self::NEW_NAME))->once();

        $theme = new Theme($settingsService, $sessionService);
        $response = $theme->updateName(self::NEW_NAME);

        $this->assertFalse($response['error']);
        $this->assertEquals(Theme::NAME_UPDATED, $response['message']);
    }

    public function testUpdateNamePreviousNameShouldUpdateWithNewName()
    {
        $sessionService = \Mockery::mock('App\Service\SessionService');
        $settingsService = \Mockery::mock('App\Service\SettingsService');
        $session = \Mockery::mock('Symfony\Component\HttpFoundation\Session\Session');
        $sessionArray = array('username' => 'test', 'role' => 5);
        $sessionService->shouldReceive('getNewSession')->andReturn($session);
        $session->shouldReceive('isStarted')->andReturn(false);
        $session->shouldReceive('start')->once();
        $session->shouldReceive('get')->andReturn($sessionArray);
        $settingsService->shouldReceive('getSetting')
            ->withArgs(array('site.name'))->andReturn(self::CURRENT_NAME);
        $settingsService->shouldReceive('updateSetting')
            ->withArgs(array('site.name', self::NEW_NAME))->once();

        $theme = new Theme($settingsService, $sessionService);
        $response = $theme->updateName(self::NEW_NAME);

        $this->assertFalse($response['error']);
        $this->assertEquals(Theme::NAME_UPDATED, $response['message']);
    }
}