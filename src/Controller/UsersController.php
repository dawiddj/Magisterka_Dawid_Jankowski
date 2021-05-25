<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UsersController
 *
 * Kontroler obsługujący tworzenie, edycję, wyświetlanie i obecność użytkowników;
 *
 * @package App\Controller
 */
class UsersController extends BasicController
{
    protected $page = [
        'title' => 'Użytkownicy'
    ];

    /**
     * Pobieranie listy użytkowników
     *
     * @IsGranted("ROLE_ADMIN")
     * @Route("/users/list", name="app_users_list")
     */
    public function displayList()
    {
        return $this->render('users/list.html.twig', [
            'users' => $this->loadRepository('App:User')->findAll()
        ]);
    }

    /**
     * Edycja użytkownika
     *
     * @IsGranted("ROLE_ADMIN")
     * @Route("/users/editor/{id}", name="app_users_edit", defaults={"id":0})
     */
    public function edit(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->loadRepository('App:User')->findOneBy(['id' => $request->get('id')]);
        $isNew = false;

        if (!$user) {
            $user = new User();
            $isNew = true;
        }

        $form = $this->createForm(UserType::class, $user, ['user_exists' => ($user && $user->getId())]);
        $errors = [];

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $userImage = $user->getUserImage();

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();

                    // nowy użytkownik - haszujemy jego hasło;
                    if ($isNew) {
                        $userPassword = $passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData());

                        $user->setPassword($userPassword);
                        $em->persist($user);
                        $em->flush();
                    }

                    if ($userImage) { // zapis avatara użytkownika
                        $userImageOriginalName = $userImage->getClientOriginalName();
                        $userImageName = preg_replace('/[^a-zA-Z0-9\_\-\.]/', '_', $userImageOriginalName);

                        $imageDirectory = $this->getParameter('public_dir').'/public/user_images/'.join('/', str_split($user->getId(), 1)).'/';

                        // usuwamy stare zdjęcie
                        if ($user->getImageName()) {
                            $oldUserImage = $imageDirectory.$user->getImageName();

                            if (file_exists($oldUserImage)) {
                                unlink($oldUserImage);
                            }
                        }

                        if (!file_exists($imageDirectory)) { // tworzymy katalog ze zdjęciami użytkownika - jeśli nie istnieje
                            mkdir($imageDirectory, 0775, true);
                        }

                        $userImage->move($imageDirectory, $userImageName); // kopiujemy

                        $user->setImageName($userImageName);
                        $em->persist($user);
                        $em->flush();
                    }

                    $userPersonalities = $user->getFirstName().' '.$user->getLastName();

                    if ($isNew) {
                        $this->addFlash('success', 'Konto użytkownika "'.$userPersonalities.'" zostało utworzone.');
                    } else {
                        $this->addFlash('success', 'Zmiany w koncie "'.$userPersonalities.'" zostały zapisane.');
                    }

                    return new RedirectResponse($this->generateUrl('app_users_edit', ['id' => $user->getId()]));
                } else {
                    $errors = $this->extractFormErrors($form);
                }
            }
        }

        return $this->render('users/editor.html.twig', [
            'form' => $form->createView(),
            'userTasks' => $this->loadRepository('App:Task')->findBy(['assignedTo' => $user]),
            'formUser' => $user,
            'errors' => $errors
        ]);
    }

    /**
     * Usuwanie użytkownika
     *
     * @IsGranted("ROLE_ADMIN")
     * @Route("/users/delete/{id}", name="app_users_delete")
     */
    public function delete(Request $request)
    {
        $user = $this->loadRepository('App:User')->findOneBy(['id' => $request->get('id')]);

        if ($user) { // znaleziono użytkownika;
            $userPersonalities = $user->getFirstName().' '.$user->getLastName();
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'Użytkownik "'.$userPersonalities.'" został usunięty.');
        } else {
            $this->addFlash('error', 'Nie znaleziono zadanego użytkownika');
        }

        return new RedirectResponse($this->generateUrl('app_users_list'));
    }

    /**
     * Odnotowywanie obecności użytkownika (uruchamiane co ok. 2 sekundy);
     *
     * @Route("/users/update_activity", name="app_users_update_activity", options={"expose"=true})
     */
    public function updateActivity(Request $request)
    {
        $user = $this->loadRepository('App:User')->findOneBy(['id' => $request->get('user_id')]);

        if ($user) {
            $user->setLastActiveAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return $this->json(['success' => 1]);
    }
}