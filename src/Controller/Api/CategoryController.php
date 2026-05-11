<?php

namespace App\Controller\Api;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/categories')]
final class CategoryController extends AbstractController
{
    #[Route('', name: 'api_categories_list', methods: ['GET'])]
    public function list(CategoryRepository $categoryRepository): JsonResponse
    {
        $categories = $categoryRepository->findAll();

        return $this->json(array_map(fn($c) => [
            'id'           => $c->getId(),
            'name'         => $c->getName(),
            'slug'         => $c->getSlug(),
            'productCount' => $c->getProductCount(),
        ], $categories));
    }
}
