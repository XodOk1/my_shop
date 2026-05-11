<?php

namespace App\Controller\GSM;

use App\Entity\GSM\TripSheet;
use App\Form\GSM\TripOrderType;
use App\Form\GSM\TripSheetType;
use App\Repository\TripSheetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\UX\Turbo\TurboBundle;

#[Route('/trip/sheet', name: 'trip_sheet_')]

class TripSheetController extends AbstractController
{
    private TripSheetRepository $tsRepo;
    private EntityManagerInterface $em;
    private CsrfTokenManagerInterface $csrf;
    public function __construct(TripSheetRepository $tsRepo, EntityManagerInterface $em, CsrfTokenManagerInterface $csrf)
    {
        $this->tsRepo = $tsRepo;
        $this->em = $em;
        $this->csrf = $csrf;
    }



    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        CsrfTokenManagerInterface $csrf
    ): Response {
        // $frameId = $request->headers->get('Turbo-Frame');
        // dump($frameId);
        // $token = $request->request->get('_token');
        // if (!$csrf->isTokenValid(new CsrfToken('trip_sheet_create', $token))) {
        //     return new Response('Bad CSRF token', 403);
        // }

        // $form = $this->createForm(TripSheetType::class, new TripSheet(), [
        //     'action' => $this->generateUrl('trip_sheet_create'),
        // ]);
        $tripSheet = new TripSheet();
        $form = $this->createForm(TripSheetType::class, $tripSheet, [
            'action' => $this->generateUrl('trip_sheet_create'),
        ]);


        dump($request->getPreferredFormat());
        dump(TurboBundle::STREAM_FORMAT);
        $tripSheets = $this->tsRepo->findAll();
        if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
            return $this->render('trip_sheet/stream/create.stream.html.twig', [
                'tripSheet' => $tripSheets,
            ]);
        }
        return $this->render('gsm/index.html.twig', [
            'tripSheets' => $tripSheets,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        CsrfTokenManagerInterface $csrf
    ): Response {
        $frameId = $request->headers->get('Turbo-Frame');
        dump($frameId);
        $token = $request->request->get('_token');
        dump($frameId);

        $tripSheet = new TripSheet();
        $form = $this->createForm(TripSheetType::class, $tripSheet);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            // важно для Turbo: 422 чтобы форма перерисовалась с ошибками
            return $this->render('gsm/_form.html.twig', [
                'form' => $form
            ], new Response('', 422));
        }


        if (!$csrf->isTokenValid(new CsrfToken('trip_sheet_create', $token))) {
            return new Response('Bad CSRF token', 403);
        }


        $em->persist($tripSheet);
        $em->flush();

        if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
            return $this->render('gsm/stream/create.stream.html.twig', [
                'tripSheet' => $tripSheet,
            ]);
        }
        return $this->redirectToRoute(
            'trip_sheet_index',

            ['form' => $form]
        );
    }


    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, int $id): Response
    {
        $frameId = $request->headers->get('Turbo-Frame');
        dump($frameId);

        $form = $this->createForm(TripSheetType::class, $id, [
            'action' => $this->generateUrl('trip_sheet_edit', ['id' => $id])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // save...

            return $this->redirectToRoute('trip_sheet_edit');
        }

        $response = new Response(null, $form->isSubmitted() ? 422 : 200);

        return $this->render('gsm/edit.html.twig', [
            'form' => $form,
        ], $response);
    }

    #[Route('/new', name: 'new')]
    public function newProduct(Request $request): Response
    {
        $form = $this->createForm(TripOrderType::class, null, [
            'action' => $this->generateUrl('product_new'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // save...

            return $this->redirectToRoute('product_list');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form,
        ]);
    }
}
