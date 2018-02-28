<?php

namespace App\Controller;

/**
 * Controller for the registration page
 * @author Christopher Bitler
 */
class RegisterController extends UserController
{
    public function index() {
        parent::setupUser();
        return $this->render('register.html.twig', $this->getTemplateVariables());
    }
}
