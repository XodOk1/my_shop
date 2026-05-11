<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['car:read']],
    denormalizationContext: ['groups' => ['car:write']]
)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['car:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['car:read', 'car:write'])]
    private ?string $brand = null;

    #[ORM\Column(length: 255)]
    #[Groups(['car:read', 'car:write'])]
    private ?string $model = null;

    #[ORM\Column(length: 20)]
    #[Groups(['car:read', 'car:write'])]
    private ?string $licensePlate = null;

    #[ORM\Column]
    #[Groups(['car:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['car:read', 'car:write'])]
    private ?\DateTimeImmutable $closedAt = null;

    #[ORM\Column]
    #[Groups(['car:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: TripList::class, mappedBy: 'car')]
    #[Groups(['car:read'])]
    private Collection $tripLists;

    #[ORM\OneToMany(targetEntity: Driver::class, mappedBy: 'car')]
    #[Groups(['car:read'])]
    private Collection $drivers;

    public function __construct()
    {
        $this->tripLists = new ArrayCollection();
        $this->drivers = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getLicensePlate(): ?string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): static
    {
        $this->licensePlate = $licensePlate;
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
            $tripList->setCar($this);
        }

        return $this;
    }

    public function removeTripList(TripList $tripList): static
    {
        if ($this->tripLists->removeElement($tripList)) {
            if ($tripList->getCar() === $this) {
                $tripList->setCar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Driver>
     */
    public function getDrivers(): Collection
    {
        return $this->drivers;
    }

    public function addDriver(Driver $driver): static
    {
        if (!$this->drivers->contains($driver)) {
            $this->drivers->add($driver);
            $driver->setCar($this);
        }

        return $this;
    }

    public function removeDriver(Driver $driver): static
    {
        if ($this->drivers->removeElement($driver)) {
            if ($driver->getCar() === $this) {
                $driver->setCar(null);
            }
        }

        return $this;
    }
}
