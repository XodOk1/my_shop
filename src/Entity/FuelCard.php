<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\FuelCardRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FuelCardRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['fuelcard:read']],
    denormalizationContext: ['groups' => ['fuelcard:write']]
)]
class FuelCard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fuelcard:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['fuelcard:read', 'fuelcard:write'])]
    private ?float $liters = null;

    #[ORM\Column]
    #[Groups(['fuelcard:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['fuelcard:read', 'fuelcard:write'])]
    private ?\DateTimeImmutable $closedAt = null;

    #[ORM\Column]
    #[Groups(['fuelcard:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: TripList::class, inversedBy: 'fuelCards')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fuelcard:read', 'fuelcard:write'])]
    private ?TripList $tripList = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLiters(): ?float
    {
        return $this->liters;
    }

    public function setLiters(?float $liters): static
    {
        $this->liters = $liters;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getClosedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeImmutable $closedAt): static
    {
        $this->closedAt = $closedAt;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTripList(): ?TripList
    {
        return $this->tripList;
    }

    public function setTripList(?TripList $tripList): static
    {
        $this->tripList = $tripList;

        return $this;
    }
}
