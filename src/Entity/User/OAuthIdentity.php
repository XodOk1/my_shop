<?php

namespace App\Entity\User;

use App\Repository\User\OAuthIdentityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OAuthIdentityRepository::class)]
class OAuthIdentity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $provider = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $provider_id = null;

    #[ORM\ManyToOne(inversedBy: 'oAuthIdentity')]
    private ?User $user = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    public function getProviderId(): ?string
    {
        return $this->provider_id;
    }

    public function setProviderId(string $provider_id): static
    {
        $this->provider_id = $provider_id;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
