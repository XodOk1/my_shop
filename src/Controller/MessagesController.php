<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MessagesController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function new(Request $request): Response
    {
        // return new Response('respons', Response::HTTP_UNPROCESSABLE_ENTITY);
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'Ex: John Doe'],
                'constraints' => [
                    new NotBlank(['message' => 'Заполните поле']),
                    new Length(['min' => 2])
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Ex: johndoe@mail.ru'],
                'constraints' => [
                    new NotBlank(['message' => 'Заполните поле']),
                    new Email,
                ]
            ])
            ->add('message', TextareaType::class, [
                'attr' => ['placeholder' => 'Ex: Pleace enter your message here...'],
                'constraints' => [
                    new NotBlank(['message' => 'Заполните поле']),
                    new Length(['min' => 10])
                ]
            ])
            ->getForm();


        // $form->handleRequest($request);
        return $this->handleForm(
            $form,
            $request,
            function ($form, $data) {
                dump(sprintf('Incoming email from %s <%s>', $data['name'], $data['email']));


                $this->addFlash('seccess', 'Message sent! We.');

                return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
            },
            function ($form, $data) {
                return $this->render('messages/new.html.twig', [
                    'form' => $form->createView(),
                    'controller_name' => 'MessagesController',
                ]);
            }
        );
    }





    public function handleForm(FormInterface $form, Request $request, callable $onSuccess, callable $render): Response
    {
        $form->handleRequest($request);

        $submitted = $form->isSubmitted();
        $data = $form->getData();
        if ($submitted && $form->isValid()) {
            return $onSuccess($form, $data);
        }

        $response = $render($form, $data);
        dump($response->getStatusCode());
        if ($submitted && 200 === $response->getStatusCode()) {
            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $response;
    }
}
