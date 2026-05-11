<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Product\Product;
use App\Entity\SubtitleTrack;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\User\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routeName: 'admin', routePath: '/admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly ProductRepository  $productRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly UserRepository     $userRepository,
    ) {}

    public function index(): Response
    {
        $products      = $this->productRepository->findAll();
        $productCount  = count($products);
        $inStock       = count(array_filter($products, fn($p) => $p->getAmount() > 0));

        return $this->render('admin/dashboard.html.twig', [
            'productCount'  => $productCount,
            'categoryCount' => count($this->categoryRepository->findAll()),
            'userCount'     => count($this->userRepository->findAll()),
            'inStock'       => $inStock,
            'outOfStock'    => $productCount - $inStock,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<span style="font-family:Georgia,serif;font-style:italic">Мы_работаем</span>')
            ->renderContentMaximized();
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('admin/admin.css');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Дашборд', 'fa fa-th-large');

        yield MenuItem::section('Магазин');
        yield MenuItem::linkToCrud('Товары', 'fas fa-box-open', Product::class);
        yield MenuItem::linkToCrud('Категории', 'fas fa-tags', Category::class);

        yield MenuItem::section('Контент');
        yield MenuItem::linkToCrud('Субтитры', 'fas fa-closed-captioning', SubtitleTrack::class);
    }
}
