<?php

namespace App\Controller;

use App\Entity\Audit;
use App\Form\AuditType;
use App\Repository\AuditRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/audit")
 */
class AuditController extends AbstractController
{
    /**
     * @Route("/", name="audit_index", methods={"GET"})
     */
    public function index(AuditRepository $auditRepository): Response
    {
        return $this->render('audit/index.html.twig', [
            'audits' => $auditRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="audit_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $audit = new Audit();
        $form = $this->createForm(AuditType::class, $audit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($audit);
            $entityManager->flush();

            return $this->redirectToRoute('audit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('audit/new.html.twig', [
            'audit' => $audit,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="audit_show", methods={"GET"})
     */
    public function show(Audit $audit): Response
    {
        return $this->render('audit/show.html.twig', [
            'audit' => $audit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="audit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Audit $audit): Response
    {
        $form = $this->createForm(AuditType::class, $audit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('audit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('audit/edit.html.twig', [
            'audit' => $audit,
            'form' => $form,
        ]);
    }
}
