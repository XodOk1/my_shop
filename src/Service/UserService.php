<?php

namespace App\Service;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
    ) {}

    public function isValidEmail(string $email): bool
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function register(string $email, string $password, array $roles = []): User
    {
        if ($this->userRepository->findOneBy(['email' => $email])) {
            throw new \DomainException('Адрес электронный почты уже занят');
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        if ($roles) {
            $user->setRoles($roles);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
