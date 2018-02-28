<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 2/16/2018
 * Time: 12:01 PM
 */

namespace App\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use App\Service\SessionService;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionServiceTest extends TestCase
{
    public function testGetNewSessionShouldReturnSessionObject()
    {
        $sessionService = new SessionService();
        $session = $sessionService->getNewSession();

        $this->assertTrue($session instanceof Session);
    }
}