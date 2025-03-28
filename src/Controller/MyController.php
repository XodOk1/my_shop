<?php

namespace App\Controller;

use App\common\TestData;
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use PharIo\Manifest\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MyController extends AbstractController
{
    #[Route('/product/{id}', name: 'my_product',)]
    public function getCategoryById(int $id): Response
    {

        return $this->render('my/index.html.twig', [
            'items' => [],
            'categoryItems' => []
        ]);
    }


    #[Route('/', name: 'app_my',)]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $dataClient = new TestData($entityManager);
        $dataClient->getProducts($entityManager);

        $productItems = $entityManager->getRepository(Product::class)->findAll();
        $categoryItems = $entityManager->getRepository(Category::class)->findAll();


        return $this->render('my/index.html.twig', [
            'items' => $productItems,
            'categoryItems' =>$categoryItems,
        ]);
    }
}
