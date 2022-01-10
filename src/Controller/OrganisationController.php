<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\OrganisationUserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/organisation")
 */
class OrganisationController extends AbstractController
{
    /**
     * @Route("/", name="organisation_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('organisation/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="organisation_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(OrganisationUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            if ($form->get('isManager')->getData()) {
                $user->setRoles(['ROLE_MANAGER']);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('organisation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('organisation/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="organisation_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('organisation/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="organisation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(OrganisationUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $plainPassword
                    )
                );
            }
            if ($form->get('isManager')->getData()) {
                $user->setRoles(['ROLE_MANAGER']);
            } else {
                $user->setRoles([]);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('organisation_index', [], Response::HTTP_SEE_OTHER);
        } else if (in_array('ROLE_MANAGER', $user->getRoles())) {
            $form->get('isManager')->setData(true);
        }

        return $this->renderForm('organisation/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="organisation_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('organisation_index', [], Response::HTTP_SEE_OTHER);
    }
}
