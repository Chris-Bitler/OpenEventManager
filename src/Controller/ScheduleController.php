<?php

namespace App\Controller;

use App\Entity\ScheduleItem;
use App\Service\ScheduleService;
use App\Service\SessionService;

/**
 * Controller for the schedule page
 * @author Christopher Bitler
 */
class ScheduleController extends UserController
{
    /** This is the date() string for datetime-local for html5 */
    const dateFormat = 'Y-m-d\TH:i:s';

    /** @var ScheduleService */
    private $scheduleService;

    public function __construct(ScheduleService $scheduleService = null)
    {
        $this->scheduleService = $scheduleService ?: new ScheduleService();
    }

    /**
     * Render the schedule page, or redirect if they are not an admin
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|
     *              \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        parent::setupUser();
        $sessionService = new SessionService();
        $session = $sessionService->getNewSession();
        $data = array(
            'dateTime' => date(self::dateFormat, time()),
            'items' => $this->scheduleService->getScheduleItems()
        );
        $this->mergeToTemplateVariables($data);

        if ($session->get('user') !== null) {
            if ($session->get('user')['role'] == 5) {
                return $this->render('admin/schedule.html.twig', $this->getTemplateVariables());
            } else {
                return $this->redirect('/');
            }
        } else {
            return $this->redirect('/');
        }
    }
}
