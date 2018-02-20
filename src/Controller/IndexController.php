<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 2/13/2018
 * Time: 10:05 AM
 */

namespace App\Controller;


use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    public function index() {
        $session = (new SessionService())->getNewSession();
        if (!$session->isStarted()) $session->start();
        if ($session->get('username')) {
            return $this->render('index.html.twig', array(
                'username' => $session->get('username')
            ));
        } else {
            return $this->render('index.html.twig');
        }
    }
}