<?php

namespace App\Controller;

use App\Entity\Posts;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="app_dashboard")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        // Manage response
        $em = $this->getDoctrine()->getManager();
        // get posts or post
        $query = $em->getRepository(Posts::class)->findAllPosts();
        // Pagination with KNP
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), /*page number*/
            1 /*limit per page*/
        );

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'Bienvenido a Dashboard',
            'pagination' => $pagination
        ]);
    }
}
