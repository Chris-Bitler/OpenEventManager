<?php

namespace App\Tests\Unit\Entity;

use App\Entity\SiteSetting;
use PHPUnit\Framework\TestCase;

class SiteSettingTest extends TestCase
{
    const KEY = 'test';
    const VALUE = 'test-value';

    private function generateSiteSetting()
    {
        return new SiteSetting(self::KEY, self::VALUE);
    }

    public function testGetKeyShouldReturnKey()
    {
        $setting = $this->generateSiteSetting();

        $this->assertEquals(self::KEY, $setting->getKey());
    }

    public function testSetKeyShouldUpdateKey()
    {
        $setting = $this->generateSiteSetting();
        $currentKey = $setting->getKey();

        $setting->setKey('test2');

        $this->assertNotEquals($currentKey, $setting->getKey());
    }

    public function testGetValueShouldUpdateValue()
    {
        $setting = $this->generateSiteSetting();

        $this->assertEquals(self::VALUE, $setting->getValue());
    }

    public function testSetValueShouldUpdateValue()
    {
        $setting = $this->generateSiteSetting();
        $currentValue = $setting->getValue();

        $setting->setValue('test2');

        $this->assertNotEquals($currentValue, $setting->getValue());
    }
}