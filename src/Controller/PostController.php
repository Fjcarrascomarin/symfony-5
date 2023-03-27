<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


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
            $photoFile = $form['photo']->getData();
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photoFile) {

                // this is needed to safely include the file name as part of the URL
                $originalFilename = pathinfo($photoFile->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0080-\uffff] remove',$originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    throw new \Exception("Ups!. Algo ha salido mal");
                }
            }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $post->setPhoto($newFilename);

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

    /**
     * @Route("/post/{id}", name="app_post")
     */
    public function verPost($id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Posts::class)->find($id);
        return $this->render('post/verPost.html.twig',['post'=>$post]);
    }

    /**
     * @Route("/own-post", name="MisPost")
     */
    public function MisPost(){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $post = $em->getRepository(Posts::class)->findBy(['user'=>$user]);
        return $this->render('post/own-post.html.twig',['post'=>$post]);
    }
}
