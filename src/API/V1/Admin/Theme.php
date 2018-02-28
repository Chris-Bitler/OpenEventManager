<?php

namespace App\API\V1\Admin;

use App\Service\SessionService;
use App\Service\SettingsService;

/**
 * API for updating theme information such as background color, text color
 * and name
 * @author Christopher Bitler
 */
class Theme
{

    const NOT_AUTHORIZED = 'Not authorized to access this endpoint.';
    const THEME_COLOR_UPDATED = 'Theme updated';
    const NAME_UPDATED = 'Name updated';

    /** @var SettingsService */
    private $settingsService;

    /** @var SessionService */
    private $sessionService;

    /**
     * Create the Theme API with the necessary services
     * @param SettingsService $settingsService
     * @param SessionService $sessionService
     */
    public function __construct(
        SettingsService $settingsService = null,
        SessionService $sessionService = null
    )
    {
        $this->settingsService = $settingsService ?: new SettingsService();
        $this->sessionService = $sessionService ?: new SessionService();
    }

    /**
     * Update the site's color in the site settings
     * @param string $color Hex Code for the site color
     * @param int $textColor Number representing the text color from 0 (black) to 255 (white)\
     * @return array API response with error indicator and message
     */
    public function updateColor($color, $textColor)
    {
        $session = $this->sessionService->getNewSession();
        if(!$session->isStarted()) $session->start();
        $user = $session->get('user');
        if($user && $user['role'] == 5) {
            $currentColor = $this->settingsService->getSetting('theme.color');
            $currentTextColor = $this->settingsService->getSetting('theme.textColor');

            if ($currentColor == null) {
                $this->settingsService->insertSetting('theme.color', $color);
            } else {
                $this->settingsService->updateSetting('theme.color', $color);
            }

            if ($currentTextColor === null) {
                $this->settingsService->insertSetting('theme.textColor', $textColor);
            } else {
                $this->settingsService->updateSetting('theme.textColor', $textColor);
            }

            return $this->generateReturnArray(false, self::THEME_COLOR_UPDATED);
        } else {
            return $this->generateReturnArray(true, self::NOT_AUTHORIZED);
        }
    }

    /**
     * Update the site's name
     * @param string $name The new name for the site
     * @return array The API response with a value indicating if it is an error response and a message
     */
    public function updateName($name) {
        $session = $this->sessionService->getNewSession();
        if(!$session->isStarted()) $session->start();
        $user = $session->get('user');
        if($user && $user['role'] == 5) {
            $currentName = $this->settingsService->getSetting('site.name');
            if ($currentName == null) {
                $this->settingsService->insertSetting('site.name', $name);
            } else {
                $this->settingsService->updateSetting('site.name', $name);
            }

            return $this->generateReturnArray(false, self::NAME_UPDATED);
        } else {
            return $this->generateReturnArray(true, self::NOT_AUTHORIZED);
        }
    }

    /**
     * Generate an array to be returned from the API
     * @param bool $error True if it is an error, false if not
     * @param string $message The message to return
     * @return array The generated values for returning from the API
     */
    private function generateReturnArray($error, $message)
    {
        return array(
            'error' => $error,
            'message' => $message
        );
    }
}