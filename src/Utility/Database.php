<?php


namespace App\Utility;


use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

/**
 * This is a super hacky way to get access to the Doctrine EntityManager in
 * the JSON-RPC api. Since it's not easily possible to access the entity manager that
 * symfony uses outside of a symfony controller or service. It effectively creates
 * a second EntityManager, separate from one the symfony is using.
 * @author Christopher Bitler
 */
class Database
{

    /**
     * Create a new EntityManager from doctrine using the symfony configuration
     * @return EntityManager The new EntityManager
     * @throws \Doctrine\ORM\ORMException Any error during the database connection process
     */
    public function createDoctrineObject()
    {
        $config = new Configuration();
        $config->setEntityNamespaces(array('Entities' => 'App\Entity'));
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(array(),false));
        $config->setQueryCacheImpl(new ArrayCache());
        $config->setMetadataCacheImpl(new ArrayCache());
        $config->setProxyDir("data/proxy");
        $config->setProxyNamespace("App\Data\Proxy");
        $connectionParams = array(
            'url' => getenv('DATABASE_URL')
        );

        return EntityManager::create($connectionParams,$config);
    }
}
