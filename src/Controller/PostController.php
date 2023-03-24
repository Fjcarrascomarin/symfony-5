<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/post-register", name="app_post")
     */
    public function index(Request $request): Response
    {
        // Init new Post
        $post = new Posts();

        // Init new form
        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);

        // Validate form
        if($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();
            $post->setUser($user);
            $em= $this->getDoctrine()->getManager();
            $em-> persist($post);
            $em->flush();
            $this->addFlash('success', 'Post añadido con éxito');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
            'formularioPost' => $form->createView()
        ]);
    }
}
