<?php

namespace App\Controller\Admin;

use App\Entity\Product\ProductImage;
use App\Service\ProductImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/product-images')]
class ProductImageAdminController extends AbstractController
{
    #[Route('/{id}/delete', name: 'admin_product_image_delete', methods: ['POST'])]
    public function delete(
        ProductImage $image,
        Request $request,
        ProductImageService $imageService,
        EntityManagerInterface $em,
    ): Response {
        if (!$this->isCsrfTokenValid('delete_image_' . $image->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Неверный токен безопасности.');
            return $this->redirect($request->headers->get('referer', '/admin'));
        }

        $productId = $image->getProduct()->getId();
        $imageService->deleteImage($image, $em);

        $this->addFlash('success', 'Фотография удалена.');

        $referer = $request->request->get('_referer', '/admin');
        return $this->redirect($referer);
    }
}
