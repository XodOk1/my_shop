<?php

namespace App\Controller\Voice;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/voice')]
class VoiceController extends AbstractController
{
    #[Route('/index',name: 'app_voice_index', methods: ['GET'])]
    public function index(): Response
    {


        return $this->render('voice/index.html.twig', [

        ]);
    }

    // #[Route('/new', name: 'app_movie_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $date = new DateTimeImmutable();
    //     $movie = new Movie();
    //     $form = $this->createForm(MovieType::class, $movie);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $movie->setCreatedAt($date);
    //         $entityManager->persist($movie);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_movie_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('movie/new.html.twig', [
    //         'movie' => $movie,
    //         'form' => $form,
    //     ]);
    // }
}