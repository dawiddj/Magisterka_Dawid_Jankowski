<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class BasicController extends AbstractController
{
    protected $page = [];

    protected NotificationRepository $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * Pobiera i zwraca odpowiedni obiekt repozytorium;
     *
     * @param string $repositoryName
     * @return ObjectRepository|null
     */
    protected function loadRepository(string $repositoryName): ?ObjectRepository
    {
        return $this->getDoctrine()->getRepository($repositoryName);
    }

    /**
     * Wyciąga błędy z przesłanych formularzy i zwraca jako tablicę pole => komunikat
     *
     * @param FormInterface $form
     * @return array
     */
    protected function extractFormErrors(FormInterface $form) : array
    {
        $errors = $form->getErrors(false, true);
        $errorArray = [];

        if ($errors) {
            foreach ($errors as $error) {
                $errorArray[] = [
                    'message' => $error->getMessage()
                ];
            }
        }

        return $errorArray;
    }

    /**
     * Renders a view.
     *
     * @final
     */
    protected function render(string $view, array $parameters = array(), Response $response = null): Response
    {
        $parameters = array_merge($parameters, [
            'page' => $this->page,
            'loggedUser' => $this->getUser(),
            'notifications' => $this->notificationRepository->findUnreadNotifications($this->getUser())
        ]);

        if ($this->container->has('templating')) {
            $content = $this->container->get('templating')->render($view, $parameters);
        } elseif ($this->container->has('twig')) {
            $content = $this->container->get('twig')->render($view, $parameters);
        } else {
            throw new \LogicException('You can not use the "render" method if the Templating Component or the Twig Bundle are not available. Try running "composer require symfony/twig-bundle".');
        }

        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }
}