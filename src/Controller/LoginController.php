<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Controller for the login page
 * @author Christopher Bitler
 */
class LoginController extends Controller
{
    public function index() {
        return $this->render('login.html.twig');
    }
}
