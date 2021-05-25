<?php

namespace App\Handler;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;

class MessageHandler extends BaseEntityHandler
{
    protected $entityName = 'Message';

    /** @var MessageRepository */
    protected $repository;

    /**
     * @param string $content
     * @param User $user
     * @return Message
     */
    public function saveMessage(string $content, User $user): Message
    {
        $message = (new Message())
            ->setContent($content)
            ->setCreatedBy($user);

        $this->save($message);

        return $message;
    }
}