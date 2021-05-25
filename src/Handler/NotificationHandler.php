<?php

namespace App\Handler;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;

class NotificationHandler extends BaseEntityHandler
{
    protected $entityName = 'Notification';

    /** @var NotificationRepository */
    protected $repository;

    public function createNotification(string $title, string $content, User $user): Notification
    {
        $notification = (new Notification())
            ->setTitle($title)
            ->setContent($content)
            ->setUser($user);

        return $this->save($notification);
    }

    public function setNotificationRead(Notification $notification): void
    {
        $notification->setRead(true);
        $this->save($notification);
    }
}