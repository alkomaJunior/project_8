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

use App\Entity\Task;
use App\Form\TaskType;
use App\Helper\UrlManagerTrait;
use App\Repository\TaskRepository;
use App\Service\Cache\CacheValidationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Manage task contents.
 *
 * @Route("/tasks")
 *
 * @IsGranted("ROLE_USER")
 */
class TaskController extends AbstractController
{
    use UrlManagerTrait;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * TaskController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("", name="task_list_all")
     * @Route("/done/{!isDone}", name="task_list", requirements={"isDone"="true|false"})
     *
     * @param CacheValidationInterface $cache
     * @param TaskRepository           $repo
     * @param null|string              $isDone
     *
     * @return Response
     */
    public function listAction(CacheValidationInterface $cache, TaskRepository $repo, ?string $isDone = null): Response
    {
        return $cache->set(
            $this->render('task/list.html.twig', [
                'tasks' => $repo->findTasks($isDone),
                'isDone' => $isDone,
            ])
        );
    }

    /**
     * @Route("/create", name="task_create")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list', ['isDone' => 'false']);
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}/edit", name="task_edit", requirements={"id"="\d+"})
     *
     * @param Task    $task
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Task $task, Request $request): Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirect(
                $this->validReferer($request->request->get('referer'), 'task_list_all')
            );
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/{id}/toggle", name="task_toggle", requirements={"id"="\d+"})
     *
     * @param Task    $task
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function toggleTaskAction(Task $task, Request $request): RedirectResponse
    {
        $flag = $task->isDone();

        $task->toggle(!$flag);
        $this->entityManager->flush();

        $this->addFlash(
            'success',
            sprintf(
                'La tâche %s a bien été marquée comme %s faite.',
                $task->getTitle(),
                ($flag) ? 'n\'est pas encore' : ''
            )
        );

        return $this->redirect(
            $this->validReferer($request->headers->get('referer'), 'task_list_all')
        );
    }

    /**
     * @Route("/{id}/delete", name="task_delete", requirements={"id"="\d+"})
     *
     * @IsGranted("DELETE", subject="task")
     *
     * @param Task    $task
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteTaskAction(Task $task, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->get('_token'))) {
            $this->entityManager->remove($task);
            $this->entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été supprimée.');

            return $this->redirect($this->validReferer(
                $request->headers->get('referer'),
                'task_list_all'
            ));
        }

        $this->addFlash('warning', 'la tâche n\'a pas été supprimée. le token n\'est pas valid!');

        return $this->redirect($this->validReferer(
            $request->headers->get('referer'),
            'task_list_all'
        ));
    }
}
