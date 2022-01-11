<?php

namespace App\Controller;

use App\Entity\GanttTask;
use App\Form\GanttTaskType;
use App\Repository\GanttTaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/gantt")
 */
class GanttTaskController extends AbstractController
{
    /**
     * @Route("/", name="gantt_task_index", methods={"GET"})
     */
    public function index(GanttTaskRepository $ganttTaskRepository): Response
    {
        $months = []; 
        $weeks = [];
        $tops = [];
        $dates = [];
        $date = new \DateTime();
        for ($i = 0; $i < 90; ++$i) {
            $dayStr = $date->format("l");
            if ($dayStr !== 'Saturday' && $dayStr !== 'Sunday') {
                $dateStr = $date->format('Y-m-d');
                $week = $date->format('W');
                [$year, $month, $day] = explode('-', $dateStr);
                $month = date("F", strtotime(date("Y") ."-". $month ."-01"));
                if (!isset($months[$month])) {
                    $months[$month] = 0;
                }
                ++$months[$month];
                if (!isset($weeks[$week])) {
                    $weeks[$week] = 0;
                }
                ++$weeks[$week];
                $tops[] = $month;
                $dates[$dateStr] = $day;
            }
            $date->add(new \DateInterval('P1D'));
        }

        $user = $this->getUser();
        $users = $user->getSupervisees()->toArray();
        array_unshift($users, $user);
        $ganttTasks = [];
        foreach ($users as $user) {
            $ganttTasks[$user->getEmail()] = $ganttTaskRepository->findBy([
                'user' => $user
            ]);
        }

        return $this->render('gantt_task/index.html.twig', [
            'gantt_tasks_by_user' => $ganttTasks,
            'dates' => $dates,
            'months' => $months,
            'weeks' => $weeks
        ]);
    }

    /**
     * @Route("/new", name="gantt_task_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ganttTask = new GanttTask();
        $form = $this->createForm(GanttTaskType::class, $ganttTask);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ganttTask);
            $entityManager->flush();

            return $this->redirectToRoute('gantt_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gantt_task/new.html.twig', [
            'gantt_task' => $ganttTask,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="gantt_task_show", methods={"GET"})
     */
    public function show(GanttTask $ganttTask): Response
    {
        return $this->render('gantt_task/show.html.twig', [
            'gantt_task' => $ganttTask,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="gantt_task_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, GanttTask $ganttTask): Response
    {
        $form = $this->createForm(GanttTaskType::class, $ganttTask);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('gantt_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gantt_task/edit.html.twig', [
            'gantt_task' => $ganttTask,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="gantt_task_delete", methods={"POST"})
     */
    public function delete(Request $request, GanttTask $ganttTask): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ganttTask->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ganttTask);
            $entityManager->flush();
        }

        return $this->redirectToRoute('gantt_task_index', [], Response::HTTP_SEE_OTHER);
    }
}
