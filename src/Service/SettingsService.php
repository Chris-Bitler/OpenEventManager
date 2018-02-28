<?php

namespace App\Service;


use App\Entity\SiteSetting;
use App\Utility\Database;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;

/**
 * Service for getting and updating setting values
 * @author Christopher Bitler
 */
class SettingsService
{
    private $entityManager;
    private $repository;

    public function __construct(
        EntityManager $entityManager = null,
        EntityRepository $repository = null
    ) {
        try {
            $this->entityManager = $entityManager ?: (new Database())->createDoctrineObject();
        } catch (ORMException $e) {
            echo $e;
        }
        $this->repository = $repository ?: $this->entityManager->getRepository('App\Entity\SiteSetting');
    }

    /**
     * Get a setting value for the site
     * @param string $key The setting to get
     * @return string|null The string value, or null if no such setting exists
     */
    public function getSetting($key)
    {
        $setting = $this->repository->findOneBy(array(
            'key' => $key
        ));

        if ($setting !== null) {
            return $setting->getValue();
        } else {
            return null;
        }
    }

    /**
     * Get a setting object for the site
     * @param string $key The setting to get
     * @return SiteSetting|null The setting object, or null if no such setting exists
     */
    public function getSettingObject($key)
    {
        $setting = $this->repository->findOneBy(array(
            'key' => $key
        ));

        if ($setting !== null) {
            return $setting;
        } else {
            return null;
        }
    }


    /**
     * Insert a setting
     * @param string $key The key for the setting
     * @param string $value The value for the setting
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function insertSetting($key, $value)
    {
        $setting = new SiteSetting($key, $value);
        $this->entityManager->persist($setting);
        $this->entityManager->flush();
    }

    /**
     * Update a setting with a new value
     * @param string $key The key for the setting to update
     * @param string $value The value for the setting to update
     * @throws \Exception If the setting does not exist
     */
    public function updateSetting($key, $value)
    {
        $setting = $this->getSettingObject($key);

        if ($setting !== null) {
            $setting->setValue($value);
            $this->entityManager->flush();
        } else {
            throw new \Exception('Attempt to update unknown setting');
        }
    }
}