<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{

    /**
     * @Route("/", name="profile_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        /** Récupère l'utilisateur actuellement connecté. Renvoie "null" si pas d'utilisateur connecté */
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $saisie = $form->get('pword')->getData(); /** Récupération des données saisie dans le champ pword */

            if ($saisie !== null) /** Si la saisie n'est pas vide */
            {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $saisie
                    )
                );
            }
            
            /** L'orm Doctrine effectue sa validation et son travail vers la base de données */
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

}
