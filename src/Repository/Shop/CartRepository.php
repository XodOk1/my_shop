<?php

namespace App\Repository\Shop;

use App\Entity\Shop\Cart;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cart>
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function findByUser(User $user): ?Cart
    {
        return $this->findOneBy(['user' => $user]);
    }

    public function findOrCreateByUser(User $user): Cart
    {
        $cart = $this->findByUser($user);

        if (!$cart) {
            $cart = new Cart($user);
            $this->getEntityManager()->persist($cart);
            $this->getEntityManager()->flush();
        }

        return $cart;
    }
}
