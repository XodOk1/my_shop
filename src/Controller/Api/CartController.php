<?php

namespace App\Controller\Api;

use App\Entity\Shop\CartItem;
use App\Repository\ProductRepository;
use App\Repository\Shop\CartItemRepository;
use App\Repository\Shop\CartRepository;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/cart')]
final class CartController extends AbstractController
{
    public function __construct(
        private readonly CartRepository     $cartRepository,
        private readonly CartItemRepository $cartItemRepository,
        private readonly ProductRepository  $productRepository,
        private readonly EntityManagerInterface $em,
    ) {}

    /** GET /api/cart — получить корзину текущего пользователя */
    #[Route('', name: 'api_cart_get', methods: ['GET'])]
    public function get(UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $cart = $this->cartRepository->findOrCreateByUser($user);

        return $this->json($cart->toArray());
    }

    /** POST /api/cart/items — добавить товар в корзину */
    #[Route('/items', name: 'api_cart_add_item', methods: ['POST'])]
    public function addItem(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data      = $request->toArray();
        $productId = $data['product'] ?? null;
        $quantity  = max(1, (int) ($data['quantity'] ?? 1));
        $scent     = $data['selectedScent'] ?? null;

        if (!$productId) {
            return $this->json(['message' => 'product is required.'], Response::HTTP_BAD_REQUEST);
        }

        $product = $this->productRepository->find($productId);
        if (!$product) {
            return $this->json(['message' => 'Product not found.'], Response::HTTP_NOT_FOUND);
        }

        if ($product->getAmount() < $quantity) {
            return $this->json(['message' => 'Not enough stock.'], Response::HTTP_CONFLICT);
        }

        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $cart = $this->cartRepository->findOrCreateByUser($user);

        // Если товар уже в корзине — увеличиваем количество
        $existing = $cart->findItemByProduct($productId);
        if ($existing) {
            $existing->setQuantity($existing->getQuantity() + $quantity);
            $this->em->flush();
            return $this->json($existing->toArray());
        }

        $item = new CartItem($cart, $product, $quantity, $scent);
        $cart->addItem($item);
        $this->em->persist($item);
        $this->em->flush();

        return $this->json($item->toArray(), Response::HTTP_CREATED);
    }

    /** PATCH /api/cart/items/{id} — изменить количество */
    #[Route('/items/{id}', name: 'api_cart_update_item', methods: ['PATCH'], requirements: ['id' => '\d+'])]
    public function updateItem(int $id, Request $request, UserRepository $userRepository): JsonResponse
    {
        $item = $this->cartItemRepository->find($id);

        if (!$item) {
            return $this->json(['message' => 'Cart item not found.'], Response::HTTP_NOT_FOUND);
        }

        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        if ($item->getCart()->getUser()->getId() !== $user->getId()) {
            return $this->json(['message' => 'Forbidden.'], Response::HTTP_FORBIDDEN);
        }

        $data     = $request->toArray();
        $quantity = (int) ($data['quantity'] ?? 1);

        $item->setQuantity($quantity);
        $this->em->flush();

        return $this->json($item->toArray());
    }

    /** DELETE /api/cart/items/{id} — удалить позицию */
    #[Route('/items/{id}', name: 'api_cart_remove_item', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function removeItem(int $id, UserRepository $userRepository): JsonResponse
    {
        $item = $this->cartItemRepository->find($id);

        if (!$item) {
            return $this->json(['message' => 'Cart item not found.'], Response::HTTP_NOT_FOUND);
        }

        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        if ($item->getCart()->getUser()->getId() !== $user->getId()) {
            return $this->json(['message' => 'Forbidden.'], Response::HTTP_FORBIDDEN);
        }

        $this->em->remove($item);
        $this->em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /** DELETE /api/cart — очистить корзину */
    #[Route('', name: 'api_cart_clear', methods: ['DELETE'])]
    public function clear(UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $cart = $this->cartRepository->findByUser($user);

        if ($cart) {
            foreach ($cart->getItems() as $item) {
                $this->em->remove($item);
            }
            $this->em->flush();
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
