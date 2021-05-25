<?php

namespace App\Controller;

use App\Entity\Notification;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends BasicController
{
    /**
     * @Route("/notification/get/{id}", name="app_notification_display")
     */
    public function getNotification(Notification $notification): JsonResponse
    {
        if (!$notification->getUser() === $this->getUser()) { // jeśli to nie jest moje powiadomienie -> wyjątek
            throw new NotFoundHttpException('Nie znaleziono takiego powiadomienia.');
        }

        return $this->json($notification->__toArray());
    }

    /**
     * @Route("/notification/read/{id}", name="app_notification_read", options={"expose":true})
     */
    public function readNotification(Notification $notification): JsonResponse
    {
        $notification->setRead(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($notification);
        $em->flush();

        return $this->json(['success' => true]);
    }
}