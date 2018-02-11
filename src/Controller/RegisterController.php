<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Controller for the registration page
 * @author Christopher Bitler
 */
class RegisterController extends Controller
{
    public function index() {
        return $this->render('register.html.twig');
    }
}
