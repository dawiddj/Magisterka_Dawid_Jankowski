<?php
namespace App\EventListener;

use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Event\Listeners\OracleSessionInit;

class OracleSessionInitListener extends OracleSessionInit
{
    /**
     * @param ConnectionEventArgs $args
     *
     * @return void
     */
    public function postConnect(ConnectionEventArgs $args)
    {
        if ($args->getConnection()->getDatabasePlatform()->getName() == 'oracle') {
            parent::postConnect($args);
        }
    }
}