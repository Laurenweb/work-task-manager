<?php

namespace App\Controller;

use App\Helper\CategoryHelper;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="task_category_index", methods={"GET"})
     */
    public function index(CategoryRepository $taskCategoryRepository): Response
    {
        $categories = $taskCategoryRepository->findBy([
            'user' => $this->getUser()->getManagedUsers()
        ]);
        $categoryByUser = CategoryHelper::sortCategoryByUser($categories);

        return $this->render('task_category/index.html.twig', [
            'categoryByUser' => $categoryByUser,
        ]);
    }

    /**
     * @Route("/new", name="task_category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $taskCategory = new Category();
        $form = $this->createForm(CategoryType::class, $taskCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($taskCategory);
            $entityManager->flush();

            return $this->redirectToRoute('task_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task_category/new.html.twig', [
            'task_category' => $taskCategory,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="task_category_show", methods={"GET"})
     */
    public function show(Category $taskCategory): Response
    {
        return $this->render('task_category/show.html.twig', [
            'task_category' => $taskCategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="task_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Category $taskCategory): Response
    {
        $form = $this->createForm(CategoryType::class, $taskCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task_category/edit.html.twig', [
            'task_category' => $taskCategory,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="task_category_delete", methods={"POST"})
     */
    public function delete(Request $request, Category $taskCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$taskCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($taskCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('task_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
