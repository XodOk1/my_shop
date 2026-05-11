<?php

namespace App\Entity;

use App\Repository\SubtitleTrackRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubtitleTrackRepository::class)]
class SubtitleTrack
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $language = null;

    #[ORM\Column(length: 255)]
    private ?string $file = null;

    #[ORM\ManyToOne]
    private ?Movie $movie = null;

    public function getId(): ?int { return $this->id; }

    public function getLanguage(): ?string { return $this->language; }

    public function setLanguage(string $language): static
    {
        $this->language = $language;
        return $this;
    }

    public function getFile(): ?string { return $this->file; }

    public function setFile(string $file): static
    {
        $this->file = $file;
        return $this;
    }

    public function getMovie(): ?Movie { return $this->movie; }

    public function setMovie(?Movie $movie): static
    {
        $this->movie = $movie;
        return $this;
    }
}
