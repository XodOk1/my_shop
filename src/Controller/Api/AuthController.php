<?php

namespace App\Controller\Api;

use App\Repository\User\UserRepository;
use App\Service\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Cookie;

#[Route('/api')]
final class AuthController extends AbstractController
{
    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
    ): JsonResponse {
        $data     = $request->toArray();
        $email    = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return $this->json(['message' => 'Email and password are required.'], Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['message' => 'Invalid credentials.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $jwtManager->create($user);

        $response = $this->json(['message' => 'Logged in successfully.']);

        $response->headers->setCookie(
            new \Symfony\Component\HttpFoundation\Cookie(
                'BEARER',        // имя cookie — совпадает с lexik_jwt_authentication.yaml
                $token,
                new \DateTime('+1 week'),
                '/',
                null,
                false,            // secure — только HTTPS
                true,            // httpOnly — JavaScript не видит
                false,
                'lax'         // sameSite — защита от CSRF
            )
        );

        return $response;
    }

    #[Route('/check', name: 'api_check', methods: ['POST'])]
    public function check(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message' => 'Unauthorized'], 401);
        }
        return $this->json(['email' => $user->getUserIdentifier()]);
    }

    #[Route('/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        $response = $this->json(['message' => 'Logged out successfully.']);

        $response->headers->clearCookie('BEARER', '/', null, true, true);

        return $response;
    }


    #[Route('/auth/vk', name: 'api_auth_vk', methods: ['POST'])]
    public function registerVk(
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
