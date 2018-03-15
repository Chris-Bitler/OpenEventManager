<?php

namespace App\Controller;

use App\Service\SessionService;

/**
 * Controller for the theming page
 * @author Christopher Bitler
 */
class ThemingController extends UserController
{
    /**
     * Render the theming page, or redirect if they are not an admin
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|
     *              \Symfony\Component\HttpFoundation\Response
     */
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
