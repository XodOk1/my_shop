<?php

namespace App\Controller\Api;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products')]
final class ProductController extends AbstractController
{
    #[Route('', name: 'api_products_list', methods: ['GET'])]
    public function list(Request $request, ProductRepository $productRepository): JsonResponse
    {
        $category = $request->query->get('category');
        $maxPrice = $request->query->getInt('maxPrice', 0) ?: null;
        $scent    = $request->query->get('scent');
        $sort     = $request->query->get('sort', 'popular');
        $page     = max(1, $request->query->getInt('page', 1));
        $limit    = min(48, max(1, $request->query->getInt('limit', 12)));

        $result = $productRepository->findWithFilters($category, $maxPrice, $scent, $sort, $page, $limit);

        return $this->json([
            'hydra:member'     => array_map(fn($p) => $p->toArray(), $result['items']),
            'hydra:totalItems' => $result['total'],
            'page'             => $page,
            'limit'            => $limit,
        ]);
    }

    #[Route('/{id}', name: 'api_products_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($product->toArray());
    }
}
