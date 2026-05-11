<?php

namespace App\Entity\Shop;

use App\Entity\Product\Product;
use App\Repository\Shop\CartItemRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
#[ORM\Table(name: 'cart_item')]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Cart $cart;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Product $product;

    #[Assert\Positive]
    #[ORM\Column]
    private int $quantity = 1;

    /** ID аромата: berg | pine | ladan | none */
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $selectedScent = null;

    #[ORM\Column]
    private DateTimeImmutable $addedAt;

    public function __construct(Cart $cart, Product $product, int $quantity = 1, ?string $scent = null)
    {
        $this->cart          = $cart;
        $this->product       = $product;
        $this->quantity      = $quantity;
        $this->selectedScent = $scent;
        $this->addedAt       = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getCart(): Cart { return $this->cart; }

    public function setCart(Cart $cart): static
    {
        $this->cart = $cart;
        return $this;
    }

    public function getProduct(): Product { return $this->product; }

    public function getQuantity(): int { return $this->quantity; }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = max(1, $quantity);
        return $this;
    }

    public function getSelectedScent(): ?string { return $this->selectedScent; }

    public function setSelectedScent(?string $scent): static
    {
        $this->selectedScent = $scent;
        return $this;
    }

    public function getAddedAt(): DateTimeImmutable { return $this->addedAt; }

    public function getLineTotal(): float
    {
        return $this->product->getPrice() * $this->quantity;
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'product'       => [
                'id'    => $this->product->getId(),
                'name'  => $this->product->getName(),
                'price' => $this->product->getPrice(),
                'kind'  => $this->product->getKind(),
                'images' => array_map(
                    fn($img) => ['id' => $img->getId(), 'filename' => $img->getFilename(), 'url' => '/uploads/products/' . $img->getFilename()],
                    $this->product->getImages()->toArray()
                ),
            ],
            'quantity'      => $this->quantity,
            'selectedScent' => $this->selectedScent,
            'lineTotal'     => $this->getLineTotal(),
            'addedAt'       => $this->addedAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
