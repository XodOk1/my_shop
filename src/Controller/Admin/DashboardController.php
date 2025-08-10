<?php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routeName: 'admin', routePath: '/admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        // опциональный редирект сразу в CRUD
        if ($this->container->has(AdminUrlGenerator::class)) {
            $url = $this->container->get(AdminUrlGenerator::class)
                ->setController(\App\Controller\Admin\MovieCrudController::class)
                ->generateUrl();
            return $this->redirect($url);
        }

        return $this->render('admin/dashboard.html.twig');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Movies', 'fas fa-film', \App\Entity\Movie::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-tags', \App\Entity\Category::class);
        yield MenuItem::linkToCrud('Subtitles', 'fas fa-closed-captioning', \App\Entity\SubtitleTrack::class);
    }
}
