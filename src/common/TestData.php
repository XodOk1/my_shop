<?php

namespace App\common;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class TestData
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getProducts()
    {
        $categoryData = $this->em->getRepository(Category::class)->findAll();
        dump($categoryData);

        if ($categoryData == []) {
            $item = $this->createCategory();
        }
    }

    private function createCategory()
    {
        $categoryList = [];

        foreach (range(1, 5) as $i) {
            $category = new Category();
            $category->setName("category $i");
            $this->em->persist($category);
            array_push($categoryList, $category);
        }

        foreach ($categoryList as $category) {
            foreach (range(1, 5) as $i) {
                $product = new Product();
                $product->setName("product $i");
                $product->setCategoryId($category);
                $this->em->persist($product);
            }

        }
        $this->em->flush();

    }


}