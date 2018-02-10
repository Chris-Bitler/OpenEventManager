<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 2/8/2018
 * Time: 8:47 AM
 */

namespace App\API\V1;

use App\Service\UserService;

class User
{
    /** @var UserService */
    private $userService;

    public function __construct(UserService $service = null)
    {
        $this->userService = $service ?: new UserService();
    }
}