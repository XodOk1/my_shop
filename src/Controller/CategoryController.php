<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    #[Route('/categories', name: 'category_list')]
    public function listCategories(): Response
    {
        $categories = $this->em->getRepository(Category::class)->findAll();
        $products = $this->em->getRepository(Product::class)->findAll();

        return $this->render('category/list.html.twig', [
            'categories' => $categories,
            'products' => $products,

        ]);
    }


    #[Route('/category/{id}', name: 'app_category')]
    public function getCategory(int $id): Response
    {
        $currentCategory = $this->em->getRepository(Category::class)->find($id);

        if (!$currentCategory) {
            throw $this->createNotFoundException('Категория не найдена');
        }

        $categories = $this->em->getRepository(Category::class)->findAll();
        $products = $this->em->getRepository(Product::class)->findBy(
            ['categoryId' => $currentCategory]);

        return $this->render('category/index.html.twig', [
            'currentCategory' => $currentCategory,
            'categories' => $categories,
            'products' => $products
        ]);
    }
}





