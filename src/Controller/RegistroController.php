<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistroController extends AbstractController
{
    // Mi redirección
    /**
     * @Route("/registro", name="app_registro")
     */
    public function index(Request $request): Response
    {
        $user = new User();
        // Add Form
        $form = $this->createForm(UserType::class,$user);
        // Add Entity Manager
        $form->handleRequest($request);
        // Submit and valid
        if($form->isSubmitted() && $form->isValid()) {
            // Set default props
            $user->setBan(false);
            $user->setRoles(['Role_user']);
            // Manage Entity
            $em = $this->getDoctrine()->getManager();
            // Persist
            $em->persist($user);
            // Clear exit buffer
            $em->flush();
            // Add Message
            $this->addFlash('success', 'Se añadió correctamente');
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
