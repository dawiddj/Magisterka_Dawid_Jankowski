<?php

namespace App\Controller;

use App\Entity\Message;
use App\Handler\FileHandler;
use App\Handler\MessageHandler;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MessengerController extends BasicController
{
    protected $page = [
        'title' => 'Komunikator'
    ];

    /**
     * @Route("/", name="app_messenger_home")
     * @Route("/messenger", name="app_messenger")
     */
    public function panel()
    {
        return $this->render('messenger/panel.html.twig');
    }

    /**
     *
     * @Route("/messenger/messages_ajax_list", name="app_messenger_messages_ajax_list", options={"expose"=true})
     */
    public function messagesList(
        Request $request,
        MessageRepository $messageRepository,
        UserRepository $userRepository
    ): JsonResponse {
        $user = $this->getUser();
        $user->setLastActiveAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $messages = $messageRepository->findMessages(
            $user,
            100,
            $request->get('time'),
            $request->get('searchPhrase')
        );

        return $this->json([
            'messages' => $messages,
            'onlineUsers' => $userRepository->findLastActiveUsers(),
            'timestamp' => time()
        ]);
    }

    /**
     * @Route("/messenger/message_save", name="app_messenger_message_save", options={"expose"=true})
     */
    public function saveMessage(Request $request, FileHandler $fileHandler, MessageHandler $messageHandler)
    {
        $content = trim($request->get('message'));
        $files = $request->files->get('files');

        if (!empty($content)) {
            $message = $messageHandler->saveMessage($content, $this->getUser());

            if ($files) {
                $fileHandler->uploadFiles($message, $files);
            }

            return $this->json(['success' => 1]);
        }

        return $this->json(['success' => 0]);
    }
}