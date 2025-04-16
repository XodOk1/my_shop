<?php

namespace App\Controller;

use App\Service\PaginatorService;
use App\common\TestData;
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use PharIo\Manifest\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class MyController extends AbstractController
{
    #[Route('/product/{id}', name: 'my_product',)]
    public function getCategoryById(int $id): \Symfony\Component\HttpFoundation\Response
    {

        return $this->render('my/index.html.twig', [
            'items' => [],
            'categoryItems' => []
        ]);
    }







    #[Route('/', name: 'app_my',)]
    public function index(Request $request,
                          EntityManagerInterface $entityManager,
                          PaginatorService $paginatorService
    ): Response {
        $dataClient = new TestData($entityManager);
        $dataClient->getProducts($entityManager);

        $productItems = $entityManager->getRepository(Product::class)->findAll();
        $categoryItems = $entityManager->getRepository(Category::class)->findAll();

        // Получаем номер страницы из запроса
        $currentPage = $request->query->getInt('page', 1);
        $limit = 10;

        // Создаём запрос для продуктов
        $queryBuilder = $entityManager->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p')
            ->orderBy('p.id', 'DESC');

        // Получаем Query объект
        $query = $queryBuilder->getQuery();
        // Используем сервис пагинации
        $paginationF = $paginatorService->paginate($queryBuilder, $currentPage, $limit);

        return $this->render('my/index.html.twig', [
            'items' => $paginationF['items'],
            'categoryItems' =>$categoryItems,
            'pagination' => [
                'current_page' => $currentPage,
                'total_pages' => $paginationF['total_pages'],
                'has_previous_page' => $currentPage > 1,
                'has_next_page' => $currentPage < $paginationF['total_pages']
            ]
        ]);
    }
}
