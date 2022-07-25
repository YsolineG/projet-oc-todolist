<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
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
    public function listAction(TaskRepository $taskRepository): Response
    {
        $user = $this->getUser();

        if($user) {
            $tasks = $user->getTasks();
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

        return new Response("Vous n'avez pas accès à cette page", 400);
    }

//    /**
//     * @Route("/{id}", name="app_task_show", methods={"GET"})
//     */
//    public function show(Task $task): Response
//    {
//        return $this->render('task/list.html.twig', [
//            'task' => $task,
//        ]);
//    }

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

        return new Response("Vous n'avez pas accès à cette page", 400);
    }

    /**
     * @Route("/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task, TaskRepository $taskRepository): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $task->toggle(!$task->isIsDone());
        $taskRepository->add($task, true);

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/{id}/delete", name="task_delete", methods={"GET"})
     */
    public function deleteTaskAction(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $user = $this->getUser();
        $userTask = $task->getUser();

        if($user === $userTask) {
            //        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $taskRepository->remove($task, true);
//        }

            $this->addFlash('success', 'La tâche a bien été supprimée.');

            return $this->redirectToRoute('task_list', [], Response::HTTP_SEE_OTHER);
        }

        return new Response("Vous n'avez pas accès à cette page", Response::HTTP_UNAUTHORIZED);
    }
}
