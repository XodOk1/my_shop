<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TripRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['trip:read']],
    denormalizationContext: ['groups' => ['trip:write']]
)]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trip:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['trip:read', 'trip:write'])]
    private ?string $orderNumber = null;

    #[ORM\Column(length: 500)]
    #[Groups(['trip:read', 'trip:write'])]
    private ?string $address = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['trip:read', 'trip:write'])]
    private ?float $distance = null;

    #[ORM\Column]
    #[Groups(['trip:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['trip:read', 'trip:write'])]
    private ?\DateTimeImmutable $closedAt = null;

    #[ORM\Column]
    #[Groups(['trip:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: TripList::class, mappedBy: 'trip')]
    #[Groups(['trip:read'])]
    private Collection $tripLists;

    public function __construct()
    {
        $this->tripLists = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDistance(?float $distance): static
    {
        $this->distance = $distance;
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

    /**
     * @return Collection<int, TripList>
     */
    public function getTripLists(): Collection
    {
        return $this->tripLists;
    }

    public function addTripList(TripList $tripList): static
    {
        if (!$this->tripLists->contains($tripList)) {
            $this->tripLists->add($tripList);
            $tripList->setTrip($this);
        }

        return $this;
    }

    public function removeTripList(TripList $tripList): static
    {
        if ($this->tripLists->removeElement($tripList)) {
            if ($tripList->getTrip() === $this) {
                $tripList->setTrip(null);
            }
        }

        return $this;
    }
}
