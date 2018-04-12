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
class ScheduleController extends UserController
{

    /** @var ScheduleService */
    private $scheduleService;

    public function __construct(
        ScheduleService $scheduleService = null
    ) {
        $this->scheduleService = $scheduleService ?: new ScheduleService();
    }

    /**
     * Render the schedule page, or redirect if they are not an admin
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        parent::setupUser();
        $data = array(
            'items' => $this->scheduleService->getScheduleItems()
        );
        $this->mergeToTemplateVariables($data);
        return $this->render('schedule.html.twig', $this->getTemplateVariables());
    }
}
