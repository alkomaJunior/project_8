<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage task contents.
 *
 * @IsGranted("ROLE_USER")
 */
class TaskController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/tasks", name="task_list_all")
     * @Route("/tasks/done/{!isDone}", name="task_list", requirements={"isDone"="true|false"})
     */
    public function listAction(TaskRepository $repository, ?string $isDone = null): Response
    {
        $tasks = $repository->findTasks($isDone);

        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
            'isDone' => $isDone,
        ]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
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
     * @Route("/tasks/{id}/edit", name="task_edit", requirements={"id"="\d+"})
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

            return $this->redirect($request->request->get('referer'));
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle", requirements={"id"="\d+"})
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

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete", requirements={"id"="\d+"})
     * @IsGranted("DELETE", subject="task")
     */
    public function deleteTaskAction(Task $task, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->get('_token'))) {
            $this->entityManager->remove($task);
            $this->entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été supprimée.');

            return $this->redirect($request->headers->get('referer'));
        }

        $this->addFlash('warning', 'la tâche n\'a pas été supprimée. le token n\'est pas valid');

        return $this->redirect($request->headers->get('referer'));
    }
}
