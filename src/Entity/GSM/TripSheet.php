<?php

namespace App\Entity\GSM;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\TripOrder;
use App\Repository\TripSheetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: TripSheetRepository::class)]
#[ApiResource]
#[Broadcast]
class TripSheet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $route = null;

    /**
     * @var Collection<int, TripOrder>
     */
    #[ORM\OneToMany(targetEntity: TripOrder::class, mappedBy: 'tripSheet', orphanRemoval: true)]
    private Collection $tripoOrders;

    public function __construct()
    {
        $this->tripoOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setid(): int
    {
        return $this->id;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(?string $route): static
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @return Collection<int, TripOrder>
     */
    public function getTripoOrders(): Collection
    {
        return $this->tripoOrders;
    }

    public function addTripoOrder(TripOrder $tripoOrder): static
    {
        if (!$this->tripoOrders->contains($tripoOrder)) {
            $this->tripoOrders->add($tripoOrder);
            $tripoOrder->setTripSheet($this);
        }

        return $this;
    }

    public function removeTripoOrder(TripOrder $tripoOrder): static
    {
        if ($this->tripoOrders->removeElement($tripoOrder)) {
            // set the owning side to null (unless already changed)
            if ($tripoOrder->getTripSheet() === $this) {
                $tripoOrder->setTripSheet(null);
            }
        }

        return $this;
    }
}
