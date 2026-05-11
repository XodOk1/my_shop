<?php

namespace App\Controller\Admin;

use App\Entity\Product\Product;
use App\Service\ProductImageService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ProductCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly ProductImageService $imageService,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Товар')
            ->setEntityLabelInPlural('Товары')
            ->setPageTitle(Crud::PAGE_INDEX, 'Каталог товаров')
            ->setPageTitle(Crud::PAGE_NEW, 'Новый товар')
            ->setPageTitle(Crud::PAGE_EDIT, fn(Product $p) => 'Редактировать: ' . $p->getName())
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['name', 'description'])
            ->setPaginatorPageSize(20)
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, fn(Action $a) => $a->setLabel('+ Добавить товар'))
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn(Action $a) => $a->setIcon('fa fa-edit')->setLabel(''))
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn(Action $a) => $a->setIcon('fa fa-trash')->setLabel(''))
            ->update(Crud::PAGE_INDEX, Action::DETAIL, fn(Action $a) => $a->setIcon('fa fa-eye')->setLabel(''));
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('category', 'Категория'))
            ->add(ChoiceFilter::new('kind', 'Тип')->setChoices(self::KIND_CHOICES))
            ->add(NumericFilter::new('price', 'Цена (копейки)'));
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->leftJoin('entity.category', 'cat')
            ->addSelect('cat')
            ->leftJoin('entity.images', 'img')
            ->addSelect('img');
    }

    private const KIND_CHOICES = [
        'Свеча формовая'     => 'candle',
        'Свеча контейнерная' => 'vessel',
        'Гипс'               => 'plaster',
        'Набор (комбо)'      => 'combo',
    ];

    private const SCENT_CHOICES = [
        'Бергамот' => 'berg',
        'Хвоя'     => 'pine',
        'Ладан'    => 'ladan',
        'Без аромата' => 'none',
    ];

    public function configureFields(string $pageName): iterable
    {
        // ── Только список ──────────────────────────────────────────────────
        yield ImageField::new('thumbnail', 'Фото')
            ->setBasePath('/')
            ->onlyOnIndex();

        yield TextField::new('name', 'Название')
            ->onlyOnIndex();

        yield AssociationField::new('category', 'Категория')
            ->onlyOnIndex();

        yield ChoiceField::new('kind', 'Тип')
            ->setChoices(self::KIND_CHOICES)
            ->renderAsBadges([
                'candle'  => 'success',
                'vessel'  => 'info',
                'plaster' => 'warning',
                'combo'   => 'danger',
            ])
            ->onlyOnIndex();

        yield IntegerField::new('price', 'Цена, ₽')
            ->formatValue(fn($v) => number_format((int)($v / 100), 0, ',', ' ') . ' ₽')
            ->onlyOnIndex();

        yield IntegerField::new('amount', 'Остаток')
            ->setTemplatePath('admin/product/amount_field.html.twig')
            ->onlyOnIndex();

        // ── Только форма ───────────────────────────────────────────────────
        yield TextField::new('name', 'Название')
            ->setColumns(8)
            ->onlyOnForms();

        yield AssociationField::new('category', 'Категория')
            ->setColumns(4)
            ->onlyOnForms();

        yield ChoiceField::new('kind', 'Тип товара')
            ->setChoices(self::KIND_CHOICES)
            ->setColumns(4)
            ->onlyOnForms();

        yield IntegerField::new('priceRubles', 'Цена (₽)')
            ->setHelp('Вводите в рублях, например: 2400')
            ->setColumns(4)
            ->onlyOnForms();

        yield IntegerField::new('amount', 'Количество на складе')
            ->setColumns(4)
            ->onlyOnForms();

        yield TextareaField::new('description', 'Описание')
            ->setNumOfRows(4)
            ->setHelp('Краткое описание товара для карточки')
            ->setColumns(12)
            ->onlyOnForms();

        yield IntegerField::new('weight', 'Вес, г')
            ->setColumns(3)
            ->onlyOnForms();

        yield IntegerField::new('burnTime', 'Горение, ч')
            ->setColumns(3)
            ->onlyOnForms();

        yield ChoiceField::new('scents', 'Ароматы')
            ->setChoices(self::SCENT_CHOICES)
            ->allowMultipleChoices()
            ->renderExpanded()
            ->setColumns(6)
            ->onlyOnForms();

        yield TextareaField::new('specsText', 'Характеристики')
            ->setHelp('Формат: «Ключ: Значение», каждая пара на новой строке')
            ->setNumOfRows(5)
            ->setColumns(12)
            ->onlyOnForms();

        yield Field::new('newImages', 'Добавить фотографии')
            ->setFormType(FileType::class)
            ->setFormTypeOptions([
                'mapped'   => false,
                'multiple' => true,
                'required' => false,
                'attr'     => [
                    'accept'   => 'image/jpeg,image/png,image/webp',
                    'class'    => 'product-image-upload',
                    'multiple' => true,
                ],
            ])
            ->setColumns(12)
            ->setTemplatePath('admin/product/images_upload_field.html.twig')
            ->onlyOnForms();
    }

    public function persistEntity(EntityManagerInterface $em, $entity): void
    {
        parent::persistEntity($em, $entity);
        $this->handleImageUploads($em, $entity);
    }

    public function updateEntity(EntityManagerInterface $em, $entity): void
    {
        parent::updateEntity($em, $entity);
        $this->handleImageUploads($em, $entity);
    }

    private function handleImageUploads(EntityManagerInterface $em, Product $product): void
    {
        $request = $this->getContext()->getRequest();
        $files   = $request->files->get('Product')['newImages'] ?? [];

        if (!is_array($files)) {
            $files = [$files];
        }

        $uploads = array_filter($files, fn($f) => $f instanceof UploadedFile);

        if ($uploads) {
            $this->imageService->uploadImages($product, $uploads, $em);
            $em->flush();
        }
    }
}
