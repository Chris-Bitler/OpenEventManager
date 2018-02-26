<?php

namespace App\Controller;

use App\Service\SessionService;

class ThemingController extends UserController
{
    public function index() {
        parent::setupUser();
        $sessionService = new SessionService();
        $session = $sessionService->getNewSession();

        if ($session->get('user') !== null) {
            if ($session->get('user')['role'] == 5) {
                return $this->render('admin.html.twig', $this->getTemplateVariables());
            } else {
                return $this->redirect('/');
            }
        } else {
            return $this->redirect('/');
        }
    }
}