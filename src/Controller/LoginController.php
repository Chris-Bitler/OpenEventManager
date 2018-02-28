<?php

namespace App\Controller;

/**
 * Controller for the login page
 * @author Christopher Bitler
 */
class LoginController extends UserController
{
    public function index() {
        parent::setupUser();
        return $this->render('login.html.twig', $this->getTemplateVariables());
    }
}
