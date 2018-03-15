<?php

namespace App\Controller;

/**
 * Controller for the login page
 * @author Christopher Bitler
 */
class LoginController extends UserController
{
    /**
     * Render the template for the login page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index() {
        parent::setupUser();
        return $this->render('login.html.twig', $this->getTemplateVariables());
    }
}
