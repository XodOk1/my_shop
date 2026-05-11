<?php

namespace App\Service;

use App\Entity\Product\Product;
use App\Entity\Product\ProductImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductImageService
{
    public function __construct(
        private readonly string $uploadDir,
    ) {}

    /** @param UploadedFile[] $files */
    public function uploadImages(Product $product, array $files, EntityManagerInterface $em): void
    {
        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $ext      = strtolower($file->guessExtension() ?? 'jpg');
            $filename = uniqid('img_', true) . '.' . $ext;

            $file->move($this->uploadDir, $filename);

            $image = new ProductImage($filename, $product);
            $em->persist($image);
            $product->addImage($image);
        }
    }

    public function deleteImage(ProductImage $image, EntityManagerInterface $em): void
    {
        $path = $this->uploadDir . '/' . $image->getFilename();
        if (file_exists($path)) {
            unlink($path);
        }
        $em->remove($image);
        $em->flush();
    }
}
