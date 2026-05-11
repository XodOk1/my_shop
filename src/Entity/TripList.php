<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\TripListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TripListRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['triplist:read']],
    denormalizationContext: ['groups' => ['triplist:write']]
)]
class TripList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['triplist:read'])]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['triplist:read', 'triplist:write'])]
    private ?int $userId = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['triplist:read', 'triplist:write'])]
    private ?string $startPoint = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['triplist:read', 'triplist:write'])]
    private ?string $typeMessage = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['triplist:read', 'triplist:write'])]
    private ?float $fuelStart = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['triplist:read', 'triplist:write'])]
    private ?float $fuelEnd = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['triplist:read', 'triplist:write'])]
    private ?float $fuelUsed = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['triplist:read', 'triplist:write'])]
    private ?float $kmStart = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['triplist:read', 'triplist:write'])]
    private ?float $kmEnd = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['triplist:read', 'triplist:write'])]
    private ?float $fuelStartFact = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['triplist:read', 'triplist:write'])]
    private ?float $fuelEndFact = null;

    #[ORM\Column]
    #[Groups(['triplist:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['triplist:read', 'triplist:write'])]
    private ?\DateTimeImmutable $closedAt = null;

    #[ORM\Column]
    #[Groups(['triplist:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Car::class, inversedBy: 'tripLists')]
    #[Groups(['triplist:read', 'triplist:write'])]
    private ?Car $car = null;

    #[ORM\ManyToOne(targetEntity: Trip::class, inversedBy: 'tripLists')]
    #[Groups(['triplist:read', 'triplist:write'])]
    private ?Trip $trip = null;

    #[ORM\ManyToOne(targetEntity: Driver::class, inversedBy: 'tripLists')]
    #[Groups(['triplist:read', 'triplist:write'])]
    private ?Driver $driver = null;

    #[ORM\OneToMany(targetEntity: FuelCard::class, mappedBy: 'tripList', cascade: ['persist', 'remove'])]
    #[Groups(['triplist:read'])]
    private Collection $fuelCards;

    public function __construct()
    {
        $this->fuelCards = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): static
    {
        $this->userId = $userId;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getStartPoint(): ?string
    {
        return $this->startPoint;
    }

    public function setStartPoint(?string $startPoint): static
    {
        $this->startPoint = $startPoint;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getTypeMessage(): ?string
    {
        return $this->typeMessage;
    }

    public function setTypeMessage(?string $typeMessage): static
    {
        $this->typeMessage = $typeMessage;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getFuelStart(): ?float
    {
        return $this->fuelStart;
    }

    public function setFuelStart(?float $fuelStart): static
    {
        $this->fuelStart = $fuelStart;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getFuelEnd(): ?float
    {
        return $this->fuelEnd;
    }

    public function setFuelEnd(?float $fuelEnd): static
    {
        $this->fuelEnd = $fuelEnd;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getFuelUsed(): ?float
    {
        return $this->fuelUsed;
    }

    public function setFuelUsed(?float $fuelUsed): static
    {
        $this->fuelUsed = $fuelUsed;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getKmStart(): ?float
    {
        return $this->kmStart;
    }

    public function setKmStart(?float $kmStart): static
    {
        $this->kmStart = $kmStart;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getKmEnd(): ?float
    {
        return $this->kmEnd;
    }

    public function setKmEnd(?float $kmEnd): static
    {
        $this->kmEnd = $kmEnd;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getFuelStartFact(): ?float
    {
        return $this->fuelStartFact;
    }

    public function setFuelStartFact(?float $fuelStartFact): static
    {
        $this->fuelStartFact = $fuelStartFact;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getFuelEndFact(): ?float
    {
        return $this->fuelEndFact;
    }

    public function setFuelEndFact(?float $fuelEndFact): static
    {
        $this->fuelEndFact = $fuelEndFact;
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

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): static
    {
        $this->trip = $trip;

        return $this;
    }

    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    public function setDriver(?Driver $driver): static
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @return Collection<int, FuelCard>
     */
    public function getFuelCards(): Collection
    {
        return $this->fuelCards;
    }

    public function addFuelCard(FuelCard $fuelCard): static
    {
        if (!$this->fuelCards->contains($fuelCard)) {
            $this->fuelCards->add($fuelCard);
            $fuelCard->setTripList($this);
        }

        return $this;
    }

    public function removeFuelCard(FuelCard $fuelCard): static
    {
        if ($this->fuelCards->removeElement($fuelCard)) {
            if ($fuelCard->getTripList() === $this) {
                $fuelCard->setTripList(null);
            }
        }

        return $this;
    }
}
