<?php

namespace App\EventListener;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\Common\EventSubscriber;

class OracleDoctrineTypeMappingListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(Events::postConnect);
    }

    public function postConnect(ConnectionEventArgs $args)
    {
        if ($args->getConnection()->getDatabasePlatform()->getName() == 'oracle') {
            $args
                ->getConnection()
                ->getDatabasePlatform()
                ->registerDoctrineTypeMapping('date', 'date');
        }
    }
}