<?php

namespace App\Entity\Product;

use App\Repository\ProductImageRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductImageRepository::class)]
class ProductImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Product $product = null;

    #[ORM\Column(length: 255)]
    private string $filename;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    public function __construct(string $filename, Product $product)
    {
        $this->filename  = $filename;
        $this->product   = $product;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getFilename(): string { return $this->filename; }

    public function getProduct(): ?Product { return $this->product; }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
}
