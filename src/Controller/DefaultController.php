<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/default/{id}/{page}', name: 'blog_default', requirements:['id' => '\d+'], methods: ['GET'], defaults: ['id' => 1])]
    public function index(Request $request, $id, $page): Response
    {
        return $this->render('default/index.html.twig', [
            'id' => $id
        ]);
    }
}
