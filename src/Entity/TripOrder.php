<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\GSM\TripSheet;
use App\Repository\TripOrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: TripOrderRepository::class)]
#[ApiResource]
#[Broadcast]
class TripOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numberOrder = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\ManyToOne(inversedBy: 'tripoOrders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TripSheet $tripSheet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberOrder(): ?int
    {
        return $this->numberOrder;
    }

    public function setNumberOrder(int $numberOrder): static
    {
        $this->numberOrder = $numberOrder;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getTripSheet(): ?TripSheet
    {
        return $this->tripSheet;
    }

    public function setTripSheet(?TripSheet $tripSheet): static
    {
        $this->tripSheet = $tripSheet;

        return $this;
    }
}
