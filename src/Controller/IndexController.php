<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 2/13/2018
 * Time: 10:05 AM
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    public function index() {
        return $this->render('index.html.twig');
    }
}