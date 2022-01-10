<?php

namespace App\Controller;

use App\Entity\Audit;
use App\Helper\CategoryHelper;
use App\Entity\Task;
use App\Entity\Category;
use App\Entity\GanttTask;
use App\Entity\User;
use App\Form\CompleteTaskType;
use App\Form\TaskEditType;
use App\Form\TaskShortCreateType;
use App\Repository\CategoryRepository;
use App\Repository\GanttTaskRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    private function createAudit(Task $task, string $action, EntityManagerInterface $entityManager) {
        $audit = new Audit();
        $audit->setUser($this->getUser());
        $audit->setTask($task);
        $audit->setCreatedAt(new DateTimeImmutable());
        $audit->setAction($action);
        $entityManager->persist($audit);
        $entityManager->flush();
    }

    public static function compareTask(Task $a, Task $b)
    {
        if ($a == $b) {
            return 0;
        }
        $dateA = $a->getDueAt();
        $dateB = $b->getDueAt();
        if ($dateA == $dateB) {
            $priorityA = $a->getPriority();
            $priorityB = $b->getPriority();
            if ($priorityA == $priorityB) {
                return $a->getId() - $b->getId();
            } else {
                return $priorityB - $priorityA;
            }
        }
        return $dateB < $dateA;
    }

    private function createTask(Task $task, MailerInterface $mailer, Category $category = null) {
        $task->setReporter($this->getUser());
        if ($category) {
            $task->addCategory($category);
        }
        $task->setExpectedDuration($task->getWantedDuration());
        $task->setActualDuration(0);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($task);
        $this->createAudit($task, "A créé la tâche", $entityManager);
        $entityManager->flush();

        $email = (new TemplatedEmail())
            ->from('tasks@xonatis.io')
            ->to(new Address($task->getAssignee()->getEmail()))
            ->subject("Création d'une nouvelle tâche")
            ->htmlTemplate('emails/create_task.html.twig')
            ->context([
                'task' => $task,
            ]);
        $mailer->send($email);
    }

    /**
     * @Route("/", name="task_index", methods={"GET", "POST"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        if (count($categories) > 0) {
            return $this->redirectToRoute('task_filter_index', [
                'id' => $categories[0]->getId()
            ]);
        }

        return $this->render('task/cat_create.html.twig');
    }

    /**
     * @Route("/category/{id}", name="task_filter_index", methods={"GET", "POST"})
     */
    public function indexWithCategory(Category $category, Request $request, CategoryRepository $categoryRepository, MailerInterface $mailer, TaskRepository $taskRepository, GanttTaskRepository $ganttTaskRepository): Response
    {
        $qProject = $request->query->get('project');
        $qDueDate = $request->query->get('dueDate');
        $task = new Task();
        $form = $this->createForm(TaskShortCreateType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->createTask($task, $mailer, $category);
            return $this->redirect($request->getUri());
        }

        $categories = $categoryRepository->findBy([
            'user' => $this->getUser()->getManagedUsers()
        ]);
        $durations = [];
        foreach ($categories as $pcategory) {
            $durations[$pcategory->getId()] = 0.0;
        }
        $categoryByUser = CategoryHelper::sortCategoryByUser($categories);
        
        if ($qDueDate && $qProject) {
            $project = $ganttTaskRepository->findOneBy([
                'name' => $qProject
            ]);
            $allTasks = $taskRepository->findBy([
                'dueAt' => new DateTime($qDueDate),
                'project' => $project
            ]);
        } else if($qDueDate) {
            $allTasks = $taskRepository->findBy([
                'dueAt' => new DateTime($qDueDate)
            ]);
        } else if($qProject) {
            $project = $ganttTaskRepository->findOneBy([
                'name' => $qProject
            ]);
            $allTasks = $taskRepository->findBy([
                'project' => $project
            ]);
        } else {
            $allTasks = $taskRepository->findAll();
        }
        $categoryDuration = $category->getDuration();
        $remainingDuration = $categoryDuration * 7 * 60;
        $currentCategoryId = $category->getId();
        $tasks = [];
        $projects = [];
        $dueDates = [];
        foreach ($allTasks as $task) {
            $categoryId = $task->getCategory()->getId();
            $durations[$categoryId] += $task->getExpectedDuration();
            if ($categoryId == $currentCategoryId) {
                $remainingDuration -= $task->getExpectedDuration();
                $tasks[] = $task;
            }
            if ($task->getCategory()->getType() != 'done') {
                $project = $task->getProject()->getName();
                $dueDate = $task->getDueAt()->format('Y-m-d');
                if (!in_array($project, $projects)) {
                    $projects[] = $project;
                }
                if (!in_array($dueDate, $dueDates)) {
                    $dueDates[] = $dueDate;
                }
            }
        }
        usort($tasks, "static::compareTask");
        sort($projects);
        sort($dueDates);

        return $this->render('task/index.html.twig', [
            'form' => $form->createView(),
            'tasks' => $tasks,
            'categoryByUser' => $categoryByUser,
            'currentCategory' => $category,
            'remainingDuration' => $remainingDuration,
            'durations' => $durations,
            'projects' => $projects,
            'dueDates' => $dueDates,
            'qProject' => $qProject,
            'qDueDate' => $qDueDate
        ]);
    }

    /**
     * @Route("/{id}", name="task_show", methods={"GET"})
     */
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Task $task): Response
    {
        $form = $this->createForm(TaskEditType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_filter_index', [
                'id' => $task->getCategory()->getId()
            ]);
        }

        return $this->renderForm('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="task_delete", methods={"POST"})
     */
    public function delete(Request $request, Task $task): Response
    {
        $categoryId = $task->getCategory()->getId();
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('task_filter_index', [
            'id' => $categoryId
        ]);
    }

    /**
     * @Route("/{id}/move/{fromCategoryId}/{toCategoryId}", name="task_move", methods={"GET"})
     */
    public function move(Task $task, string $fromCategoryId, string $toCategoryId, CategoryRepository $categoryRepository): Response
    {
        $fromCategory = $categoryRepository->find($fromCategoryId);
        $toCategory = $categoryRepository->find($toCategoryId);
        $task->setCategory($toCategory);
        $entityManager = $this->getDoctrine()->getManager();
        $this->createAudit($task, 'Déplacement de "' . $fromCategory->getTitle() . '" vers "' . $toCategory->getTitle() . '"', $entityManager);
        $entityManager->flush();
        return $this->redirectToRoute('task_filter_index', [
            'id' => $fromCategoryId
        ]);
    }
    
    /**
     * @Route("/{id}/mark/next", name="task_mark_next", methods={"GET"})
     */
    public function markNext(Task $task): Response
    {
        $oldCategory = $task->getCategory();
        $nextCategory = $task->getCategory()->getNext();
        if ($nextCategory->getType() !== 'done') {
            $entityManager = $this->getDoctrine()->getManager();
            $this->createAudit($task, 'A passé la tâche de : ' . $task->getCategory()->getTitle() . ' à :' . $nextCategory->getTitle(), $entityManager);
            $task->setCategory($nextCategory);
            $entityManager->flush();
            return $this->redirectToRoute('task_filter_index', [
                'id' => $oldCategory->getId()
            ]);
        }
        
        return $this->redirectToRoute('task_mark_done', [
            'id' => $task->getId()
        ]);
    }
    
    /**
     * @Route("/{id}/mark/done", name="task_mark_done", methods={"GET", "POST"})
     */
    public function markDone(Task $task, Request $request, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CompleteTaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nextCategory = $task->getCategory()->getNext();
            $entityManager = $this->getDoctrine()->getManager();
            $this->createAudit($task, 'A compléter la tâche', $entityManager);
            $task->setCategory($nextCategory);
            $entityManager->flush();
            return $this->redirectToRoute('task_filter_index', [
                'id' => $task->getCategory()->getId()
            ]);
        }

        $categories = $categoryRepository->findBy([
            'user' => $this->getUser()->getManagedUsers()
        ]);
        $categoryByUser = CategoryHelper::sortCategoryByUser($categories);
        $durations = [];
        foreach ($categories as $category) {
            $durations[$category->getId()] = 0.0;
            foreach ($category->getTasks() as $ptask) {
                $durations[$category->getId()] += $ptask->getExpectedDuration();
            }
        }

        return $this->renderForm('task/mark_done.html.twig', [
            'task' => $task,
            'form' => $form,
            'categoryByUser' => $categoryByUser,
            'currentCategory' => $task->getCategory(),
            'durations' => $durations
        ]);
    }
    
    /**
     * @Route("/{id}/mark/undone", name="task_mark_undone", methods={"GET"})
     */
    public function markUndone(Task $task): Response
    {
        $task->setActualDuration(0);
        $entityManager = $this->getDoctrine()->getManager();
        $this->createAudit($task, 'A revert la tâche', $entityManager);
        $entityManager->flush();
        return $this->redirectToRoute('task_filter_index', [
            'id' => $task->getCategory()->getId()
        ]);
    }

    /**
     * @Route("/dones/{email}", name="task_index_done", methods={"GET"})
     */
    public function indexDones(string $email, TaskRepository $taskRepository, UserRepository $userRepository, CategoryRepository $categoryRepository): Response
    {
        $user = $userRepository->findBy([
            'email' => $email
        ]);
        $categories = $categoryRepository->findBy([
            'user' => $this->getUser()->getManagedUsers()
        ]);
        $categoryByUser = CategoryHelper::sortCategoryByUser($categories);
        return $this->render('task/index_dones.html.twig', [
            'tasks' => $taskRepository->findBy([
                'assignee' => $user
            ]),
            'categoryByUser' => $categoryByUser,
            'currentCategory' => null
        ]);
    }
}
