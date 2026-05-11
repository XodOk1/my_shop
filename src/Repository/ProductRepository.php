<?php

namespace App\Repository;

use App\Entity\Product\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return array{items: Product[], total: int}
     */
    public function findWithFilters(
        ?string $category = null,
        ?int    $maxPrice = null,
        ?string $scent    = null,
        string  $sort     = 'popular',
        int     $page     = 1,
        int     $limit    = 12
    ): array {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->addSelect('c')
            ->leftJoin('p.images', 'i')
            ->addSelect('i');

        if ($category && $category !== 'all') {
            $qb->andWhere('c.slug = :category')
               ->setParameter('category', $category);
        }

        if ($maxPrice !== null && $maxPrice > 0) {
            // maxPrice приходит в рублях, в БД — копейки
            $qb->andWhere('p.price <= :maxPrice')
               ->setParameter('maxPrice', $maxPrice * 100);
        }

        if ($scent) {
            // scents хранятся как JSON-массив: ["berg","pine",...]
            $qb->andWhere('p.scents LIKE :scent')
               ->setParameter('scent', '%"' . $scent . '"%');
        }

        match ($sort) {
            'price-asc'  => $qb->orderBy('p.price', 'ASC'),
            'price-desc' => $qb->orderBy('p.price', 'DESC'),
            'newest'     => $qb->orderBy('p.createdAt', 'DESC'),
            default      => $qb->orderBy('p.id', 'DESC'),
        };

        $total = count($qb->getQuery()->getResult());

        $items = $qb
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return ['items' => $items, 'total' => $total];
    }
}
