<?php

/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\DataTransferObject\PasswordUpdate;
use App\Form\User\AccountType;
use App\Form\User\UpdatePasswordType;
use App\Form\User\UserType;
use App\Repository\UserRepository;
use App\Service\Cache\CacheValidationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Manage users contents.
 *
 * @Route("/users")
 *
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    private Security $security;
    private EntityManagerInterface $entityManager;

    /**
     * UserController constructor.
     *
     * @param Security               $security
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("", name="user_list")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param CacheValidationInterface $cache
     * @param UserRepository           $repository
     *
     * @return Response
     */
    public function listAction(CacheValidationInterface $cache, UserRepository $repository): Response
    {
        return $cache->set(
            $this->render(
                'user/list.html.twig',
                [
                    'users' => $repository->findAllExceptOne(
                        $this->security->getUser()->getId()
                    ),
                ]
            )
        );
    }

    /**
     * @Route("/create", name="user_create")
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", requirements={"id"="\d+"})
     *
     * @IsGranted("EDIT", subject="user")
     *
     * @param User    $user
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function editAction(User $user, Request $request): Response
    {
        $form = $this->createForm(
            AccountType::class,
            $user,
            ['update_account' => ($user === $this->getUser()) ? true : false]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié.");

            return $this->redirectToRoute(
                in_array(User::ROLE_ADMIN, $this->security->getUser()->getRoles()) ? 'user_list' : 'homepage'
            );
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    /**
     * @Route("/{id}/edit-password", name="user_password_edit", requirements={"id"="\d+"})
     *
     * @IsGranted("EDIT", subject="user")
     *
     * @param User    $user
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function editPasswordAction(User $user, Request $request): Response
    {
        $passwordUpdate = new PasswordUpdate();
        $isAccount = $user->isEqualTo($this->getUser());

        $form = $this->createForm(
            UpdatePasswordType::class,
            $passwordUpdate,
            ['update_account' => $isAccount, 'validation_groups' => [$isAccount ? 'account' : '']]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordUpdate->getNewPassword());
            $this->entityManager->flush();
            $this->addFlash('success', 'Le mot de passe a bien été modifié.');

            return $this->redirectToRoute(
                in_array(User::ROLE_ADMIN, $this->security->getUser()->getRoles()) ? 'user_list' : 'homepage'
            );
        }

        return $this->render('user/password.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
