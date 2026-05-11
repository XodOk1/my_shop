<?php

namespace App\Controller\Api;

use App\Service\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserService $userService,
        JWTTokenManagerInterface $jwtManager,
    ): JsonResponse {
        $data     = $request->toArray();
        $email    = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return $this->json(['message' => 'Email and password are required.'], Response::HTTP_BAD_REQUEST);
        }

        if (!$userService->isValidEmail($email)) {
            return $this->json(['message' => 'Invalid email format.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $userService->register($email, $password);
        } catch (\DomainException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        $response = $this->json(['message' => 'Registered successfully.'], Response::HTTP_CREATED);

        $response->headers->setCookie(new Cookie(
            'BEARER',
            $jwtManager->create($user),
            new \DateTime('+1 week'),
            '/',
            null,
            true,
            true,
            false,
            'strict'
        ));

        return $response;
    }


    
}
