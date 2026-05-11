<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\DriverRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DriverRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['driver:read']],
    denormalizationContext: ['groups' => ['driver:write']]
)]
class Driver
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['driver:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['driver:read', 'driver:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 14, nullable: true)]
    #[Groups(['driver:read', 'driver:write'])]
    private ?string $snils = null;

    #[ORM\Column(length: 20)]
    #[Groups(['driver:read', 'driver:write'])]
    private ?string $licenseDriver = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['driver:read', 'driver:write'])]
    private ?\DateTimeInterface $licenseDriverDate = null;

    #[ORM\ManyToOne(targetEntity: Car::class, inversedBy: 'drivers')]
    #[Groups(['driver:read', 'driver:write'])]
    private ?Car $car = null;

    #[ORM\OneToMany(targetEntity: TripList::class, mappedBy: 'driver')]
    #[Groups(['driver:read'])]
    private Collection $tripLists;

    public function __construct()
    {
        $this->tripLists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSnils(): ?string
    {
        return $this->snils;
    }

    public function setSnils(?string $snils): static
    {
        $this->snils = $snils;

        return $this;
    }

    public function getLicenseDriver(): ?string
    {
        return $this->licenseDriver;
    }

    public function setLicenseDriver(string $licenseDriver): static
    {
        $this->licenseDriver = $licenseDriver;

        return $this;
    }

    public function getLicenseDriverDate(): ?\DateTimeInterface
    {
        return $this->licenseDriverDate;
    }

    public function setLicenseDriverDate(?\DateTimeInterface $licenseDriverDate): static
    {
        $this->licenseDriverDate = $licenseDriverDate;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

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
            $tripList->setDriver($this);
        }

        return $this;
    }

    public function removeTripList(TripList $tripList): static
    {
        if ($this->tripLists->removeElement($tripList)) {
            if ($tripList->getDriver() === $this) {
                $tripList->setDriver(null);
            }
        }

        return $this;
    }
}
