<?php

namespace App\Entity\Product;

use App\Entity\Category;
use App\Repository\ProductRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[ORM\Column]
    private ?int $price = null;

    #[Assert\PositiveOrZero]
    #[ORM\Column]
    private int $amount = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /** candle | vessel | plaster | combo */
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $kind = null;

    /** Вес в граммах */
    #[ORM\Column(nullable: true)]
    private ?int $weight = null;

    /** Время горения в часах */
    #[ORM\Column(nullable: true)]
    private ?int $burnTime = null;

    /** ["Воск" => "Соевый, 100%", ...] */
    #[ORM\Column(type: Types::JSON)]
    private array $specs = [];

    /** ["berg", "pine", "ladan", "none"] */
    #[ORM\Column(type: Types::JSON)]
    private array $scents = [];

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: ProductImage::class, mappedBy: 'product', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $images;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->images    = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /** Возвращает цену в рублях */
    public function getPrice(): ?float
    {
        return $this->price !== null ? $this->price / 100 : null;
    }

    /** Принимает цену в копейках */
    public function setPrice(int $price): static
    {
        $this->price = $price;
        return $this;
    }

    /** Цена в копейках (для внутреннего использования) */
    public function getPriceRaw(): ?int { return $this->price; }

    public function getAmount(): int { return $this->amount; }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getKind(): ?string { return $this->kind; }

    public function setKind(?string $kind): static
    {
        $this->kind = $kind;
        return $this;
    }

    public function getWeight(): ?int { return $this->weight; }

    public function setWeight(?int $weight): static
    {
        $this->weight = $weight;
        return $this;
    }

    public function getBurnTime(): ?int { return $this->burnTime; }

    public function setBurnTime(?int $burnTime): static
    {
        $this->burnTime = $burnTime;
        return $this;
    }

    public function getSpecs(): array { return $this->specs; }

    public function setSpecs(array $specs): static
    {
        $this->specs = $specs;
        return $this;
    }

    public function getScents(): array { return $this->scents; }

    public function setScents(array $scents): static
    {
        $this->scents = $scents;
        return $this;
    }

    public function getCategory(): ?Category { return $this->category; }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }

    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getImages(): Collection { return $this->images; }

    public function addImage(ProductImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }
        return $this;
    }

    public function removeImage(ProductImage $image): static
    {
        $this->images->removeElement($image);
        return $this;
    }

    // ── Хелперы для формы EasyAdmin ────────────────────────────

    /** Цена в рублях (для поля формы) */
    public function getPriceRubles(): ?int
    {
        return $this->price !== null ? (int) round($this->price / 100) : null;
    }

    public function setPriceRubles(?int $rubles): static
    {
        $this->price = $rubles !== null ? $rubles * 100 : null;
        return $this;
    }

    /** Характеристики как «Ключ: Значение» по одной на строку */
    public function getSpecsText(): string
    {
        $lines = [];
        foreach ($this->specs as $key => $value) {
            $lines[] = "$key: $value";
        }
        return implode("\n", $lines);
    }

    public function setSpecsText(?string $text): static
    {
        $specs = [];
        foreach (explode("\n", (string) $text) as $line) {
            $line = trim($line);
            if ($line && str_contains($line, ':')) {
                [$key, $value] = explode(':', $line, 2);
                $specs[trim($key)] = trim($value);
            }
        }
        $this->specs = $specs;
        return $this;
    }

    /** URL первой фотографии — для миниатюры в списке */
    public function getThumbnail(): ?string
    {
        $first = $this->images->first();
        return $first ? '/uploads/products/' . $first->getFilename() : null;
    }

    // ── Сериализация ────────────────────────────────────────────

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'price'       => $this->getPrice(),
            'amount'      => $this->amount,
            'description' => $this->description,
            'kind'        => $this->kind,
            'weight'      => $this->weight,
            'burnTime'    => $this->burnTime,
            'specs'       => $this->specs,
            'scents'      => $this->scents,
            'category'    => $this->category ? [
                'id'   => $this->category->getId(),
                'name' => $this->category->getName(),
                'slug' => $this->category->getSlug(),
            ] : null,
            'images'      => array_map(
                fn(ProductImage $img) => [
                    'id'       => $img->getId(),
                    'filename' => $img->getFilename(),
                    'url'      => '/uploads/products/' . $img->getFilename(),
                ],
                $this->images->toArray()
            ),
            'createdAt'   => $this->createdAt->format(\DateTimeInterface::ATOM),
            'updatedAt'   => $this->updatedAt?->format(\DateTimeInterface::ATOM),
        ];
    }
}
