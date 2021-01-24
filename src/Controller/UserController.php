<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\AccountType;
use App\Form\User\EditPasswordType;
use App\Form\User\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Controller used to manage users contents.
 */
class UserController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/users", name="user_list")
     * @IsGranted("ROLE_ADMIN")
     */
    public function listAction(UserRepository $repository): Response
    {
        $users = $repository->findAllExceptOne($this->security->getUser());

        return $this->render('user/list.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/users/create", name="user_create")
     * @IsGranted("ROLE_ADMIN")
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
     * @Route("/users/{id}/edit", name="user_edit", requirements={"id"="\d+"})
     * @IsGranted("EDIT", subject="user")
     *
     * @return RedirectResponse|Response
     */
    public function editAction(User $user, Request $request): Response
    {
        $form = $this->createForm(AccountType::class, $user, ['logged_user' => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute($this->redirectLoggedUser($this->security->getUser()->getRoles()));
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    /**
     * @Route("/users/{id}/edit-password", name="user_password_edit", requirements={"id"="\d+"})
     * @IsGranted("EDIT", subject="user")
     *
     * @return RedirectResponse|Response
     */
    public function editPasswordAction(User $user, Request $request): Response
    {
        $form = $this->createForm(EditPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Le mot de passe a bien été modifié');

            return $this->redirectToRoute($this->redirectLoggedUser($this->security->getUser()->getRoles()));
        }

        return $this->render('user/password.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    /**
     * Redirect user according to his role.
     */
    private function redirectLoggedUser(array $roles): string
    {
        return in_array('ROLE_ADMIN', $roles) ? 'user_list' : 'homepage';
    }
}
