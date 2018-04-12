<?php

namespace App\Controller;

use App\Entity\ScheduleItem;
use App\Service\ScheduleService;
use App\Service\SessionService;
use App\Service\TimezoneService;

/**
 * Controller for the schedule admin page
 * @author Christopher Bitler
 */
class ScheduleAdminController extends UserController
{
    /** This is the date() string for datetime-local for html5 */
    const DATE_FORMAT = 'Y-m-d\TH:i';

    /** @var ScheduleService */
    private $scheduleService;

    /** @var TimezoneService */
    private $timezoneService;

    public function __construct(
        ScheduleService $scheduleService = null,
        TimezoneService $timezoneService = null
    ) {
        $this->scheduleService = $scheduleService ?: new ScheduleService();
        $this->timezoneService = $timezoneService ?: new TimezoneService();
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
        $timezone = new \DateTimeZone($this->timezoneService->getCurrentTimezone());
        $dateTime = new \DateTime("now", $timezone);
        $data = array(
            'dateTime' => $dateTime->format(self::DATE_FORMAT),
            'items' => $this->scheduleService->getScheduleItems(),
            'timezones' => $this->timezoneService->getTimezones(),
            'curTimezone' => $this->timezoneService->convertPHPTimezoneToFriendly(
                $this->timezoneService->getCurrentTimezone()
            )
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
