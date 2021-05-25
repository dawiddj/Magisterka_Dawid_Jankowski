<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskAdminType;
use App\Form\TaskStudentType;
use App\Handler\FileHandler;
use App\Handler\NotificationHandler;
use App\Handler\TaskHandler;
use Doctrine\Common\Util\Debug;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController
 *
 * Kontroler sterujący zadaniami związanymi z edycją i przydzielaniem zadań/tasków;
 *
 * @package App\Controller
 */
class TaskController extends BasicController
{
    protected $page = [
        'title' => 'Zadania'
    ];

    /**
     * Pobieranie listy tasków/zadań
     *
     * @Route("/tasks/list", name="app_tasks_list")
     */
    public function panel()
    {
        $repository = $this->loadRepository('App:Task');

        if ($this->isGranted('ROLE_ADMIN')) { // obecny użytkownik to administrator -> pobieramy wszystkie zadania;
            $tasks = $repository->findAll();
        } elseif ($this->isGranted('ROLE_USER')) { // obecny użytkownik to student -> pobieramy tylko jego zadania;
            $tasks = $repository->findBy(['assignedTo' => $this->getUser()]);
        }

        return $this->render('tasks/list.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /**
     * Edycja taska
     *
     * @IsGranted("ROLE_ADMIN")
     * @Route("/tasks/editor/{id}", name="app_tasks_editor", defaults={"id":0})
     */
    public function editor(
        Request $request,
        TaskHandler $taskHandler,
        NotificationHandler $notificationHandler,
        FileHandler $fileUploadHandler
    ): Response {
        $task = $this->loadRepository('App:Task')->findOneBy(['id' => $request->get('id')]);
        $isNew = false;

        if (!$task) {
            $task = new Task();
            $isNew = true;
        }

        $form = $this->createForm(TaskAdminType::class, $task, ['task' => $task]);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $deadlineAt = $form->get('deadlineAt')->getData();

                    if ($deadlineAt) {
                        $task->setDeadlineAt(new \DateTime($deadlineAt));
                    }

                    $taskHandler->save($task);
                    $fileUploadHandler->uploadFiles(
                        $task, $form->get('files')->getData()
                    );

                    if ($isNew) {
                        $this->addFlash('success', 'Zadanie "'.$task->getName().'" zostało utworzone.');
                        $notificationHandler->createNotification(
                            'SYSTEM',
                            'Zostałeś przydzielony do nowego zadania: '.$task->getName(),
                            $task->getAssignedTo()
                        );
                    } else {
                        $this->addFlash('success', 'Zmiany w zadaniu "'.$task->getName().'" zostały zapisane.');
                        $notificationHandler->createNotification(
                            'SYSTEM',
                            'Pojawiły się zmiany w zadaniu '.$task->getName().'. Zapoznaj się z nimi.',
                            $task->getAssignedTo()
                        );
                    }

                    return new RedirectResponse($this->generateUrl('app_tasks_list'));
                } else {
                    $errors = $this->extractFormErrors($form);
                }
            } else {
                $errors[] = 'Nieprawidłowy formularz';
            }
        }

        return $this->render('tasks/editor.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
            'errors' => $errors ?? []
        ]);
    }

    /**
     * Zmiana statusu taska
     *
     * @Route("/tasks/change_status/{id}", name="app_tasks_change_status", defaults={"id":0})
     */
    public function changeStatus(Request $request)
    {
        $task = $this->loadRepository('App:Task')->findOneBy(['id' => $request->get('id')]);
        $form = $this->createForm(TaskStudentType::class, $task, ['task' => $task]);

        // jeśli zagląda tu student, a zadanie jest nieprzypisane lub przypisane do kogoś innego - blokujemy;
        if (!$task || !$task->getAssignedTo() || (!$this->isGranted('ROLE_ADMIN') && $task->getAssignedTo() !== $this->getUser())) {
            throw new NotFoundHttpException('Nie znaleziono podanego zadania');
        }

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $finishedAt = $form->get('finishedAt')->getData();

                    if ($task->getProgress() == 100) {
                        $task->setStatus('finished');
                    }

                    if ($task->getStatus() == 'finished' && empty($finishedAt)) {
                        $task->setFinishedAt(new \DateTime());
                    } elseif (!empty($finishedAt)) {
                        $task->setFinishedAt(new \DateTime($finishedAt));
                    } else {
                        $task->setFinishedAt(null);
                    }

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($task);
                    $em->flush();

                    $this->addFlash('success', 'Zmiany w statusie zadania "'.$task->getName().'" zostały zapisane');

                    return new RedirectResponse($this->generateUrl('app_tasks_change_status', ['id' => $task->getId()]));
                } else {
                    $errors = $this->extractFormErrors($form);
                }
            } else {
                $errors[] = 'Nieprawidłowy formularz.';
            }
        }

        return $this->render('tasks/status.html.twig', [
            'form' => $form->createView(),
            'formTask' => $task,
            'errors' => $errors ?? []
        ]);
    }

    /**
     * Edycja taska
     *
     * @IsGranted("ROLE_ADMIN")
     * @Route("/tasks/delete/{id}", name="app_tasks_delete")
     */
    public function delete(Request $request)
    {
        $task = $this->loadRepository('App:Task')->findOneBy(['id' => $request->get('id')]);

        if ($task) {
            $taskName = $task->getName();
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();

            $this->addFlash('success', 'Zadanie "'.$taskName.'" zostało usunięte.');
        }

        return new RedirectResponse($this->generateUrl('app_tasks_list'));
    }
}