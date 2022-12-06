<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="display_pub")
     */
    public function index(): Response
    {
        
            $pubs = $this->getDoctrine()->getManager()->getRepository()->findAll();
        return $this->render('publication/index.html.twig', [
            'controller_name' => 'PublicationController',['p'=>$pubs]
        ]);
    }

    /**
     * @Route("/admin", name="display_admin")
     */
    public function indexAdmin(): Response
    {

        return $this->render('Admin/index.html.twig');
    }
}
