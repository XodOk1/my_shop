<?php

namespace App\Controller\Admin;

use App\Entity\Movie;
use App\Entity\SubtitleTrack;
use App\Enum\MovieStatus;
use App\Message\TranscodeMessage;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class MovieCrudController extends AbstractCrudController
{
    public function __construct(
        private FilesystemOperator $ingest, // Flysystem storage "ingest"
        private FilesystemOperator $media,  // Flysystem storage "media"
        private MessageBusInterface $bus,   // для dispatch() сообщения
    ) {}

    public static function getEntityFqcn(): string
    {
        return Movie::class;
    }

    public function configureFields(string $pageName): iterable
    {
        // обычные поля
        $fields = [
            TextField::new('title'),
            TextField::new('slug'),
            TextareaField::new('synopsis')->hideOnIndex(),
            IntegerField::new('releaseYear')->hideOnIndex(),
            IntegerField::new('durationSeconds')->hideOnIndex(),
            UrlField::new('posterUrl')->hideOnIndex(),
            UrlField::new('hlsMasterUrl')->onlyOnDetail(), // появится после транскода
            ChoiceField::new('status')->setChoices([
                'Ingested' => MovieStatus::Ingested,
                'Processing' => MovieStatus::Processing,
                'Ready' => MovieStatus::Ready,
                'Archived' => MovieStatus::Archived,
            ])->hideOnIndex(),
            AssociationField::new('categories')->hideOnIndex(),
        ];

        // виртуальные поля загрузки (не маппятся на сущность)
        $fields[] = Field::new('masterUpload', 'Мастер-файл (MP4/MKV)')
            ->setFormType(FileType::class)
            ->setFormTypeOptions([
                'mapped'      => false,
                'required'    => $pageName === 'new',
                'constraints' => [new Assert\File([
                    'maxSize'   => '5G',
                    'mimeTypes' => ['video/mp4','video/x-matroska','video/quicktime'],
                ])],
            ])
            ->onlyOnForms();

        $fields[] = Field::new('enSubtitleUpload', 'EN субтитры (SRT/VTT)')
            ->setFormType(FileType::class)
            ->setFormTypeOptions(['mapped' => false, 'required' => false])
            ->onlyOnForms();

        $fields[] = Field::new('ruSubtitleUpload', 'RU субтитры (SRT/VTT)')
            ->setFormType(FileType::class)
            ->setFormTypeOptions(['mapped' => false, 'required' => false])
            ->onlyOnForms();

        return $fields;
    }

    public function persistEntity(EntityManagerInterface $em, $entity): void
    {
        if (!$entity instanceof Movie) {
            parent::persistEntity($em, $entity);
            return;
        }

        $req = $this->getContext()->getRequest();

        /** @var UploadedFile|null $master */
        $master = $req->files->get('Movie')['masterUpload'] ?? null;
        /** @var UploadedFile|null $en */
        $en = $req->files->get('Movie')['enSubtitleUpload'] ?? null;
        /** @var UploadedFile|null $ru */
        $ru = $req->files->get('Movie')['ruSubtitleUpload'] ?? null;

        // базовый путь хранения этого фильма
        $id = (string) $entity->getId(); // ULID/UUID
        $entity->setStorageBasePath("media/movies/{$id}/");
        $entity->setStatus(MovieStatus::Ingested);

        // 1) кладём мастер в ingest: <id>/source.(mp4|mkv)
        if ($master instanceof UploadedFile) {
            $ext = strtolower($master->guessExtension() ?: 'mp4');
            $stream = fopen($master->getPathname(), 'r');
            $this->ingest->writeStream($id."/source.$ext", $stream);
            is_resource($stream) && fclose($stream);
        }

        // 2) сабы сразу в публичный media (пусть лежат рядом с будущим HLS)
        $base = $entity->getStorageBasePath(); // media/movies/<id>/
        if ($en instanceof UploadedFile) {
            $enExt = strtolower($en->guessExtension() ?: 'vtt'); // можно потом конвертить srt->vtt в воркере
            $stream = fopen($en->getPathname(), 'r');
            $this->media->writeStream($base."subs/en.$enExt", $stream);
            is_resource($stream) && fclose($stream);

            // если используешь отдельную сущность SubtitleTrack:
            // $entity->addSubtitleTrack(new SubtitleTrack($entity, 'en', "/$base"."subs/en.$enExt"));
        }
        if ($ru instanceof UploadedFile) {
            $ruExt = strtolower($ru->guessExtension() ?: 'vtt');
            $stream = fopen($ru->getPathname(), 'r');
            $this->media->writeStream($base."subs/ru.$ruExt", $stream);
            is_resource($stream) && fclose($stream);
            // $entity->addSubtitleTrack(new SubtitleTrack($entity, 'ru', "/$base"."subs/ru.$ruExt"));
        }

        $entity->setHlsMasterUrl('/'.$base.'master.m3u8');
        // 3) сохраняем карточку
        parent::persistEntity($em, $entity);
        $em->flush();

        // 4) запускаем фоновый транскод
        $this->bus->dispatch(new TranscodeMessage($entity->getId()));
        // если хочешь без MessageBus: $this->dispatchMessage(...) тоже ок
    }
}
