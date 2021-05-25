<?php

namespace App\Handler;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\TaskRepository;

class TaskHandler extends BaseEntityHandler
{
    protected $entityName = 'Task';

    /** @var TaskRepository */
    protected $repository;
}