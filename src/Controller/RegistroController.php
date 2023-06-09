<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Hash password
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistroController extends AbstractController
{
    // Mi redirección
    /**
     * @Route("/registro", name="app_registro")
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        // Add Form
        $form = $this->createForm(UserType::class,$user);
        // Add Entity Manager
        $form->handleRequest($request);
        // Submit and valid
        if($form->isSubmitted() && $form->isValid()) {
            // Set default props

            // Init Password Hash
            $generatePassword = $form->get('password')->getData();
            // Hash user Password
            $hashPassword = $passwordHasher->hashPassword(
                $user,
                $generatePassword
            );
            // Set haspassword after success`s submit
            $user->setPassword($hashPassword);
            // Manage Entity
            $em = $this->getDoctrine()->getManager();
            // Persist
            $em->persist($user);
            // Clear exit buffer
            $em->flush();
            // Add Message
            $this->addFlash('success', User::REGISTER_SUCCESS);
            // Redirect
            return $this->redirectToRoute('app_registro');
        }
        // Render
        return $this->render('registro/index.html.twig', [
            'controller_name' => 'RegistroController',
            'formulario' => $form->createView()
        ]);
    }
}
