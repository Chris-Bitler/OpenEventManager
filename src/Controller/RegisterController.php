<?php

namespace App\Controller;

/**
 * Controller for the registration page
 * @author Christopher Bitler
 */
class RegisterController extends UserController
{
    /**
     * Render the template for the registration page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index() {
        parent::setupUser();
        return $this->render('register.html.twig', $this->getTemplateVariables());
    }
}
