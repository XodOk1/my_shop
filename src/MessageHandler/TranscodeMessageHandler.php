<?php

namespace App\MessageHandler;

use App\Message\TranscodeMessage;
use App\Repository\MovieRepository;
use App\Enum\MovieStatus;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Process\Process;

// #[AsMessageHandler]
final class TranscodeMessageHandler
{
    public function __construct(
        private MovieRepository $movies,
        private EntityManagerInterface $em,
        // #[Autowire(service: 'flysystem.storage.ingest')]
        // private FilesystemOperator $ingestStorage,

        // #[Autowire(service: 'flysystem.storage.media')]
        // private FilesystemOperator $mediaStorage,
        private FilesystemOperator $ingest, // будет подставлен из services.yaml
        private FilesystemOperator $media,
    ) {}

    public function __invoke(TranscodeMessage $msg): void
    {
        $movie = $this->movies->find($msg->movieId);
        if (!$movie) return;

        $movie->setStatus(MovieStatus::Processing);
        $this->em->flush();

        $id = (string)$movie->getId();
        // 1) Скачиваем мастер во временный файл
        $sourceTmp = sys_get_temp_dir() . "/{$id}_source.mkv";
        $stream = $this->ingest->readStream($id . '/source.mkv') ?: $this->ingest->readStream($id . '/source.mp4');
        if (!$stream) { /* обработка ошибки */
            return;
        }
        $out = fopen($sourceTmp, 'w');
        stream_copy_to_stream($stream, $out);
        fclose($stream);
        fclose($out);

        // 2) Запускаем ffmpeg → HLS в локальный tmp-вывод
        $outDir = sys_get_temp_dir() . "/hls_{$id}";
        @mkdir($outDir . '/v0', 0777, true);
        @mkdir($outDir . '/v1', 0777, true);
        @mkdir($outDir . '/v2', 0777, true);
        @mkdir($outDir . '/v3', 0777, true);

        $cmd = [
            'ffmpeg',
            '-y',
            '-i',
            $sourceTmp,
            '-filter_complex',
            "[0:v]split=4[v1080][v720][v480][v360];" .
                "[v1080]scale=w=1920:h=1080:force_original_aspect_ratio=decrease[v0];" .
                "[v720] scale=w=1280:h=720: force_original_aspect_ratio=decrease[v1];" .
                "[v480] scale=w=854:h=480:  force_original_aspect_ratio=decrease[v2];" .
                "[v360] scale=w=640:h=360:  force_original_aspect_ratio=decrease[v3]",
            // … маппинги рендишнов …
            '-map',
            '[v0]',
            '-map',
            '0:a:0?',
            '-c:v:0',
            'libx264',
            '-b:v:0',
            '6000k',
            '-c:a:0',
            'aac',
            '-b:a:0',
            '192k',
            '-map',
            '[v1]',
            '-map',
            '0:a:0?',
            '-c:v:1',
            'libx264',
            '-b:v:1',
            '3000k',
            '-c:a:1',
            'aac',
            '-b:a:1',
            '160k',
            '-map',
            '[v2]',
            '-map',
            '0:a:0?',
            '-c:v:2',
            'libx264',
            '-b:v:2',
            '1500k',
            '-c:a:2',
            'aac',
            '-b:a:2',
            '128k',
            '-map',
            '[v3]',
            '-map',
            '0:a:0?',
            '-c:v:3',
            'libx264',
            '-b:v:3',
            '800k',
            '-c:a:3',
            'aac',
            '-b:a:3',
            '96k',
            '-f',
            'hls',
            '-hls_time',
            '4',
            '-hls_playlist_type',
            'vod',
            '-hls_flags',
            'independent_segments',
            '-hls_segment_filename',
            "{$outDir}/v%v/seg_%06d.ts",
            '-master_pl_name',
            'master.m3u8',
            '-var_stream_map',
            'v:0,a:0,name:1080p v:1,a:1,name:720p v:2,a:2,name:480p v:3,a:3,name:360p',
            "{$outDir}/v%v/prog_index.m3u8",
        ];

        $process = new Process($cmd);
        $process->setTimeout(null);
        $process->run();
        if (!$process->isSuccessful()) {
            $movie->setStatus(MovieStatus::Archived); // или свой статус Failed
            $this->em->flush();
            return;
        }

        // 3) Заливаем результат в public/media/movies/<id>/
        $base = $movie->getStorageBasePath(); // "media/movies/<id>/"
        $this->uploadDir($outDir, $base);

        // 4) Обновляем ссылку на master.m3u8
        $movie->setHlsMasterUrl('/' . $base . 'master.m3u8');
        $movie->setStatus(MovieStatus::Ready);
        $this->em->flush();

        // очистка tmp …
    }

    private function uploadDir(string $localDir, string $remoteBase): void
    {
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($localDir, \FilesystemIterator::SKIP_DOTS));
        foreach ($rii as $file) {
            /** @var \SplFileInfo $file */
            $path = $file->getPathname();
            $rel  = ltrim(str_replace($localDir, '', $path), DIRECTORY_SEPARATOR);
            $stream = fopen($path, 'r');
            $this->media->writeStream($remoteBase . $rel, $stream);
            if (is_resource($stream)) fclose($stream);
        }
    }
}
