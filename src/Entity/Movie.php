<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Metadata as API;
use App\Enum\MovieStatus;

#[API\ApiResource(
    normalizationContext: ['groups' => ['movie:read']],
    denormalizationContext: ['groups' => ['movie:write']],
    operations: [
        new API\GetCollection(uriTemplate: '/movies'),
        new API\Get(uriTemplate: '/movies/{id}'),
    ]
)]
#[API\ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'categories.slug' => 'exact',
    'status' => 'exact',
    'releaseYear' => 'exact',
])]
#[API\ApiFilter(OrderFilter::class, properties: ['releaseYear', 'ratingAvg'], arguments: ['orderParameterName' => 'sort'])]
#[ORM\Entity(repositoryClass: MovieRepository::class)]
#[ORM\HasLifecycleCallbacks] // важно

class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['movie:read', 'movie:write'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['movie:read', 'movie:write'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['movie:read', 'movie:write'])]
    private ?string $synopsis = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(['movie:read', 'movie:write'])]
    private ?int $releaseYear = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['movie:read', 'movie:write'])]
    private ?int $durationSeconds = null;

    #[ORM\Column(length: 1024)]
    #[Groups(['movie:read', 'movie:write'])]
    private string $storageBasePath;

    #[ORM\Column(length: 1024)]
    #[Groups(['movie:read', 'movie:write'])]
    private ?string $hlsMasterUrl = null;

    #[ORM\Column(length: 1024, nullable: true)]
    #[Groups(['movie:read', 'movie:write'])]
    private ?string $posterUrl = null;

    // #[ORM\Column(type: Types::ARRAY)]
    // #[Groups(['movie:read', 'movie:write'])]
    // private array $audioLanguages = [];

    // Категории/жанры для поиска и фильтров
    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'movies')]
    #[Groups(['movie:read', 'movie:write'])]
    private Collection $categories;

    // Рейтинг вашей платформы (аггрегируемый)
    #[ORM\Column]
    #[Groups(['movie:read', 'movie:write'])]
    private float $ratingAvg = 0.0;

    #[ORM\Column]
    #[Groups(['movie:read', 'movie:write'])]
    private int $ratingCount = 0;

    // Возрастной рейтинг (произвольно, можно вынести в enum)
    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['movie:read', 'movie:write'])]
    private ?string $ageRating = null;

    // Статус жизненного цикла (ingest → processing → ready → archived)
    #[ORM\Column(length: 32)]
    #[Groups(['movie:read', 'movie:write'])]
    private ?MovieStatus $status = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['movie:read', 'movie:write'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['movie:read', 'movie:write'])]
    private \DateTimeImmutable $updatedAt;

    // Субтитры — «мягкие» дорожки (WebVTT)
    /**
     * @var Collection<int, SubtitleTrack>
     */
    #[ORM\OneToMany(targetEntity: SubtitleTrack::class, mappedBy: 'movie')]
    private Collection $subtitleTracks;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->subtitleTracks = new ArrayCollection();

        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(?string $synopsis): static
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getReleaseYear(): ?int
    {
        return $this->releaseYear;
    }

    public function setReleaseYear(?int $releaseYear): static
    {
        $this->releaseYear = $releaseYear;

        return $this;
    }

    public function getDurationSeconds(): ?int
    {
        return $this->durationSeconds;
    }

    public function setDurationSeconds(?int $durationSeconds): static
    {
        $this->durationSeconds = $durationSeconds;

        return $this;
    }

    public function getStorageBasePath(): ?string
    {
        return $this->storageBasePath;
    }

    public function setStorageBasePath(string $storageBasePath): static
    {
        $this->storageBasePath = $storageBasePath;

        return $this;
    }

    public function getHlsMasterUrl(): ?string
    {
        return $this->hlsMasterUrl;
    }

    public function setHlsMasterUrl(string $hlsMasterUrl): static
    {
        $this->hlsMasterUrl = $hlsMasterUrl;

        return $this;
    }

    public function getPosterUrl(): ?string
    {
        return $this->posterUrl;
    }

    public function setPosterUrl(?string $posterUrl): static
    {
        $this->posterUrl = $posterUrl;

        return $this;
    }

    public function getAudioLanguages(): array
    {
        return $this->audioLanguages;
    }

    public function setAudioLanguages(array $audioLanguages): static
    {
        $this->audioLanguages = $audioLanguages;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getRatingAvg(): ?float
    {
        return $this->ratingAvg;
    }

    public function setRatingAvg(float $ratingAvg): static
    {
        $this->ratingAvg = $ratingAvg;

        return $this;
    }

    public function getRatingCount(): ?int
    {
        return $this->ratingCount;
    }

    public function setRatingCount(int $ratingCount): static
    {
        $this->ratingCount = $ratingCount;

        return $this;
    }

    public function getAgeRating(): ?string
    {
        return $this->ageRating;
    }

    public function setAgeRating(?string $ageRating): static
    {
        $this->ageRating = $ageRating;

        return $this;
    }

    public function getStatus(): ?MovieStatus
    {
        return $this->status;
    }

    public function setStatus(MovieStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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

    
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        if (!isset($this->createdAt)) {
            $this->createdAt = $now;
        }
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @return Collection<int, SubtitleTrack>
     */
    public function getSubtitleTracks(): Collection
    {
        return $this->subtitleTracks;
    }

    public function addSubtitleTrack(SubtitleTrack $subtitleTrack): static
    {
        if (!$this->subtitleTracks->contains($subtitleTrack)) {
            $this->subtitleTracks->add($subtitleTrack);
            $subtitleTrack->setMovie($this);
        }

        return $this;
    }

    public function removeSubtitleTrack(SubtitleTrack $subtitleTrack): static
    {
        if ($this->subtitleTracks->removeElement($subtitleTrack)) {
            // set the owning side to null (unless already changed)
            if ($subtitleTrack->getMovie() === $this) {
                $subtitleTrack->setMovie(null);
            }
        }

        return $this;
    }
}
