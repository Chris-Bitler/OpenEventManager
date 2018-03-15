<?php

namespace App\Controller;


use App\Service\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * This class injects some 'global' (such as theming) variables
 * into the template variables for the controller
 * @author Christopher Bitler
 */
class GlobalController extends Controller
{
    const DEFAULT_COLOR = '#343a40';
    const DEFAULT_TXT_RGB = "255";
    const DEFAULT_SITE_NAME = "Open Event Manager";

    private $templateVariables = array();

    /**
     * Set up global variables in the template variables such as
     * theme color, text color, and name
     */
    public function setupGlobalVariables() {
        $settingsService = new SettingsService();
        $themeColor = $settingsService->getSetting('theme.color') ?: self::DEFAULT_COLOR;
        $themeTextColor = $settingsService->getSetting('theme.textColor');
        $siteName = $settingsService->getSetting('site.name') ?: self::DEFAULT_SITE_NAME;

        // We can't use the ternary like above because the color can be '0'
        if ($themeTextColor === null) {
            $themeTextColor = self::DEFAULT_TXT_RGB;
        }

        $this->templateVariables['theme'] = array(
            'color' => $themeColor,
            'textColor' => $themeTextColor,
            'siteName' => $siteName
        );
    }
    /**
     * Get the template variables
     * @return array The template variables
     */
    public function getTemplateVariables() {
        return $this->templateVariables;
    }

    /**
     * Merge an array into the template variables
     * @param array $array The array to merge into the template variables
     */
    public function mergeToTemplateVariables($array) {
        $this->templateVariables = array_merge($this->templateVariables, $array);
    }

    /**
     * Set the template variables
     * @param array $variables The variables to set
     */
    public function setTemplateVariables($variables) {
        $this->templateVariables = $variables;
    }
}
