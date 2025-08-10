<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'category')]
// #[ORM\Index(columns: ['slug'], name: 'idx_category_slug')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 140, unique: true)]
    private string $slug;

    // /**
    //  * @var Collection<int, Product>
    //  */
    // #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'categoryId')]
    // private Collection $products;

    /**
     * @var Collection<int, Movie>
     */
    #[ORM\ManyToMany(targetEntity: Movie::class, mappedBy: 'categories')]
    private Collection $movies;

    public function __construct()
    {
        // $this->products = new ArrayCollection();
        $this->movies = new ArrayCollection();
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

        public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    // /**
    //  * @return Collection<int, Product>
    //  */
    // public function getProducts(): Collection
    // {
    //     return $this->products;
    // }

    // public function addProduct(Product $product): static
    // {
    //     if (!$this->products->contains($product)) {
    //         $this->products->add($product);
    //         $product->setCategoryId($this);
    //     }

    //     return $this;
    // }

    // public function removeProduct(Product $product): static
    // {
    //     if ($this->products->removeElement($product)) {
    //         // set the owning side to null (unless already changed)
    //         if ($product->getCategoryId() === $this) {
    //             $product->setCategoryId(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, Movie>
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): static
    {
        if (!$this->movies->contains($movie)) {
            $this->movies->add($movie);
            $movie->addCategory($this);
        }

        return $this;
    }

    public function removeMovie(Movie $movie): static
    {
        if ($this->movies->removeElement($movie)) {
            $movie->removeCategory($this);
        }

        return $this;
    }
}
