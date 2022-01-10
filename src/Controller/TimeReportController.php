<?php

namespace App\Controller;

use App\Entity\TimeDate;
use App\Entity\TimeDetail;
use App\Entity\TimeReport;
use App\Form\TimeReportType;
use App\Repository\CategoryRepository;
use App\Repository\TaskRepository;
use App\Repository\TimeReportRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/timereport")
 */
class TimeReportController extends AbstractController
{
    /**
     * @Route("/", name="time_report_index", methods={"GET"})
     */
    public function index(TimeReportRepository $timeReportRepository): Response
    {
        $date = new \DateTime('Monday this week');
        return $this->render('time_report/index.html.twig', [
            'time_reports' => $timeReportRepository->findAll(),
            'adate' => $date->format('Y-m-d')
        ]);
    }

    /**
     * @Route("/last/{adate}", name="time_report_new_last", methods={"GET"})
     */
    public function new_last(string $adate): Response
    {
        $date = new \DateTime($adate);
        $date->sub(new \DateInterval('P7D'));
        return $this->redirectToRoute('time_report_new', [
            'adate' => $date->format('Y-m-d')
        ]);
    }    
    
    /**
    * @Route("/next/{adate}", name="time_report_new_next", methods={"GET"})
    */
   public function new_next(string $adate): Response
   {
       $date = new \DateTime($adate);
       $date->add(new \DateInterval('P7D'));
       return $this->redirectToRoute('time_report_new', [
           'adate' => $date->format('Y-m-d')
       ]);
   }

    /**
     * @Route("/new/{adate}", name="time_report_new", methods={"GET","POST"})
     */
    public function new(string $adate, Request $request, CategoryRepository $categoryRepository): Response
    {
        $timeReport = new TimeReport();

        $dates = [];
        $date = new \DateTime($adate);
        for ($i = 0; $i < 5; ++$i) {
            $dateStr = $date->format('Y-m-d');
            $dates[] = $dateStr;
            $date->add(new \DateInterval('P1D'));
        }

        $user = $this->getUser();
        $categories = $categoryRepository->findBy([
            'user' => $user
        ]);
        $tasks = [];
        foreach($categories as $category) {
            if ($category->getType() === 'backlog') {
                continue;
            }
            foreach ($category->getTasks() as $task) {
                $detail = new TimeDetail();
                foreach ($dates as $date) {
                    $timeDate = new TimeDate();
                    $timeDate->setUser($user);
                    $timeDate->setDate(new DateTimeImmutable($date));
                    $timeDate->setDuration(0.0);
                    $detail->addTimeDate($timeDate);
                }
                $detail->setTask($task);
                $timeReport->addDetail($detail);
                $tasks[] = $task;
            }
        }

        $timeReport->setCreatedAt(new DateTimeImmutable());
        $timeReport->setStartingAt(new DateTimeImmutable($dates[0]));
        $timeReport->setEndingAt(new DateTimeImmutable($dates[count($dates) - 1]));

        $form = $this->createForm(TimeReportType::class, $timeReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach($timeReport->getDetails() as $detail) {
                foreach($detail->getTimeDates() as $timeDate) {
                    if ($timeDate->getDuration() > 0.0) {
                        $entityManager->persist($timeDate);
                    }
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('time_report_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('time_report/new.html.twig', [
            'time_report' => $timeReport,
            'form' => $form,
            'dates' => $dates,
            'tasks' => $tasks,
            'adate' => $adate
        ]);
    }

    /**
     * @Route("/{id}", name="time_report_show", methods={"GET"})
     */
    public function show(TimeReport $timeReport): Response
    {
        return $this->render('time_report/show.html.twig', [
            'time_report' => $timeReport
        ]);
    }

    /**
     * @Route("/{id}/edit", name="time_report_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TimeReport $timeReport, CategoryRepository $categoryRepository): Response
    {
        $dates = [];
        $date = new \DateTime($timeReport->getStartingAt()->format('Y-m-d'));
        for ($i = 0; $i < 5; ++$i) {
            $dateStr = $date->format('Y-m-d');
            $dates[] = $dateStr;
            $date->add(new \DateInterval('P1D'));
        }
        $tasks = [];
        $user = $this->getUser();
        $categories = $categoryRepository->findBy([
            'user' => $user
        ]);
        foreach($categories as $category) {
            if ($category->getType() === 'backlog') {
                continue;
            }
            foreach ($category->getTasks() as $task) {
                $tasks[] = $task;
            }
        }
        $form = $this->createForm(TimeReportType::class, $timeReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('time_report_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('time_report/edit.html.twig', [
            'time_report' => $timeReport,
            'form' => $form,
            'dates' => $dates,
            'tasks' => $tasks
        ]);
    }

    /**
     * @Route("/{id}", name="time_report_delete", methods={"POST"})
     */
    public function delete(Request $request, TimeReport $timeReport): Response
    {
        if ($this->isCsrfTokenValid('delete'.$timeReport->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($timeReport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('time_report_index', [], Response::HTTP_SEE_OTHER);
    }
}
