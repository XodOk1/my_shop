<?php

namespace App\Controller\Admin;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class UserController extends AbstractController
{


    public function __construct(
        private UserService $userService,

    ) {}

    #[Route('/users', name: 'api_users', methods: ['POST'])]
    public function create(
        Request $request,
    ): JsonResponse {
        $data     = $request->toArray();
        $email    = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $roles = $data['roles'] ?? null;


        if (!$email || !$password) {
            return $this->json(['message' => 'Email and password are required.'], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->userService->isValidEmail($email)) {
            return $this->json(['message' => 'Invalid email format.'], Response::HTTP_BAD_REQUEST);
        }
        try {
            $user =  $this->userService->register($email, $password, $roles);
        } catch (\DomainException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
        return $this->json([
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ], Response::HTTP_CREATED);

        // return $response;
    }
}
