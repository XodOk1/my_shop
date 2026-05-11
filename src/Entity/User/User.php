<?php

namespace App\Entity\User;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\User\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    /**
     * @var Collection<int, OAuthIdentity>
     */
    #[ORM\OneToMany(targetEntity: OAuthIdentity::class, mappedBy: 'user')]
    private Collection $oAuthIdentity;

    public function __construct()
    {
        $this->oAuthIdentity = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials(): void
    {
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, OAuthIdentity>
     */
    public function getOAuthIdentity(): Collection
    {
        return $this->oAuthIdentity;
    }

    public function addOAuthIdentity(OAuthIdentity $oAuthIdentity): static
    {
        if (!$this->oAuthIdentity->contains($oAuthIdentity)) {
            $this->oAuthIdentity->add($oAuthIdentity);
            $oAuthIdentity->setUser($this);
        }

        return $this;
    }

    public function removeOAuthIdentity(OAuthIdentity $oAuthIdentity): static
    {
        if ($this->oAuthIdentity->removeElement($oAuthIdentity)) {
            // set the owning side to null (unless already changed)
            if ($oAuthIdentity->getUser() === $this) {
                $oAuthIdentity->setUser(null);
            }
        }

        return $this;
    }
}
