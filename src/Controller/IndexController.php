<?php

namespace App\Controller;

/**
 * Controller on the main page of the site
 * @author Christopher Bitler
 */
class IndexController extends UserController
{
    /**
     * Render the template for the main page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index() {
        parent::setupUser();
        return $this->render('index.html.twig', $this->getTemplateVariables());
    }
}
