<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 2/16/2018
 * Time: 12:00 PM
 */

namespace App\Service;


use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class for generating sessions for testing.
 * @author Christopher Bitler
 */
class SessionService
{
    public function getNewSession()
    {
        return new Session();
    }
}