<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction(): Response
    {
        $tasks = $this->getDoctrine()->getRepository(Task::class)->findAll();

        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // TODO: refactoring
            $task->setUser($user)->setCreatedAt(new DateTime());
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', '<strong>Superbe !</strong> La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     *
     * @return RedirectResponse|Response
     */
    public function editAction(
        Task $task,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', '<strong>Superbe !</strong> La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     *
     * @return RedirectResponse
     */
    public function toggleTaskAction(Task $task, EntityManagerInterface $entityManager): Response
    {
        $task->toggle(!$task->isDone());
        $entityManager->flush();

        $this->addFlash('success', $this->taskFlash($task->isDone(), $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     * @IsGranted("DELETE", subject="task")
     *
     * @return RedirectResponse
     */
    public function deleteTaskAction(Task $task, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->get('_token'))) {
            /*$entityManager->remove($task);
            $entityManager->flush();*/

            $this->addFlash('success', '<strong>Superbe !</strong> La tâche a bien été supprimée.');
        } else {
            $this->addFlash('warning', 'la tâche n\'a pas été supprimée.');
        }

        return $this->redirectToRoute('task_list');
    }

    // TODO: Refactoring
    private function taskFlash(bool $isDone, string $title): string
    {
        if ($isDone) {
            return sprintf('<strong>Superbe !</strong> La tâche %s a bien été marquée comme faite.', $title);
        }

        return sprintf('<strong>Superbe !</strong> La tâche %s a bien été marquée comme n\'est pas encore faite.',
            $title);
    }
}
