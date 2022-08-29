<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tasks")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="task_list", methods={"GET"})
     */
    public function listAction(TaskRepository $taskRepository, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        if($user) {
            $tasks = $user->getTasks();

            if(in_array("ROLE_ADMIN", $user->getRoles())) {
                $anonymousUser = $userRepository->getAnonymousUser();

                $anonymousTasks = $taskRepository->findBy([
                    'user' => $anonymousUser
                ]);

                $tasks = array_merge($tasks->toArray(), $anonymousTasks);
            }

            return $this->render('task/list.html.twig', [
                'tasks' => $tasks,
            ]);
        }

        return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/create", name="task_create", methods={"GET", "POST"})
     */
    public function createAction(Request $request, TaskRepository $taskRepository): Response
    {
        $user = $this->getUser();

        if($user) {
            $task = new Task();
            $form = $this->createForm(TaskType::class, $task);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var User $user */
                $user = $this->getUser();
                $task->setUser($user);

                $taskRepository->add($task, true);

                $this->addFlash('success', 'La tâche a été bien été ajoutée.');

                return $this->redirectToRoute('task_list', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('task/create.html.twig', [
                'task' => $task,
                'form' => $form,
            ]);
        }

        return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $user = $this->getUser();
        $userTask = $task->getUser();

        if($user === $userTask) {
            $form = $this->createForm(TaskType::class, $task);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $taskRepository->add($task, true);

                $this->addFlash('success', 'La tâche a bien été modifiée.');

                return $this->redirectToRoute('task_list', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('task/edit.html.twig', [
                'task' => $task,
                'form' => $form,
            ]);
        }

        return new Response("Vous n'avez pas accès à cette page", Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task, TaskRepository $taskRepository): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $user = $this->getUser();
        $userTask = $task->getUser();

        if($user) {
            if($user === $userTask) {
                $task->toggle(!$task->isIsDone());
                $taskRepository->add($task, true);

                $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

                return $this->redirectToRoute('task_list');
            }

            $this->addFlash('error', "Vous n'avez pas accès à cette tâche.");
            return $this->redirectToRoute('task_list');
        }

        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/{id}/delete", name="task_delete", methods={"GET"})
     */
    public function deleteTaskAction(Task $task, TaskRepository $taskRepository): Response
    {
        $user = $this->getUser();
        $userTask = $task->getUser();
        if($user && ($user === $userTask || in_array("ROLE_ADMIN", $user->getRoles()))) {
            $taskRepository->remove($task, true);

            $this->addFlash('success', 'La tâche a bien été supprimée.');

            return $this->redirectToRoute('task_list', [], Response::HTTP_SEE_OTHER);
        }

        return new Response("Vous n'avez pas accès à cette page", Response::HTTP_UNAUTHORIZED);
    }
}
