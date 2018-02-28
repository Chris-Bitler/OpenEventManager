<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 2/20/2018
 * Time: 10:32 AM
 */

namespace App\Controller;


use App\Service\SessionService;
use App\Controller\GlobalController;

/**
 * Parent controller for any controller that needs logged in user information
 * @author Christopher Bitler
 */
class UserController extends GlobalController
{

    /**
     * Set up the user data from sessions
     * This is usually called via parent::setupUser() from the child controller
     */
    public function setupUser() {
        parent::setupGlobalVariables();
        $session = (new SessionService())->getNewSession();
        if (!$session->isStarted()) $session->start();
        if ($session->get('user')) {
            $variables = array(
                'username' => $session->get('user')['username'],
                'role' => $session->get('user')['role']
            );
            $this->mergeToTemplateVariables($variables);
        }
    }
}