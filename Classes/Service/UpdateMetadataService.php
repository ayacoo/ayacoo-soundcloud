<?php

declare(strict_types=1);

namespace Ayacoo\AyacooSoundcloud\Service;

use Ayacoo\AyacooSoundcloud\Domain\Repository\FileRepository;
use Ayacoo\AyacooSoundcloud\Helper\SoundcloudHelper;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOnlineMediaHelper;
use TYPO3\CMS\Core\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UpdateMetadataService
{
    public function __construct(
        protected FileRepository $fileRepository,
        protected MetaDataRepository $metadataRepository,
        protected ResourceFactory $resourceFactory,
        protected ProcessedFileRepository $processedFileRepository
    ) {
    }

    public function process(int $limit, SymfonyStyle $io): void
    {
        $soundcloudHelper = GeneralUtility::makeInstance(SoundcloudHelper::class, 'soundcloud');
        $audios = $this->fileRepository->getVideosByFileExtension('soundcloud', $limit);
        foreach ($audios as $audio) {
            $file = $this->resourceFactory->getFileObject($audio['uid']);
            $metaData = $soundcloudHelper->getMetaData($file);
            if ($metaData !== []) {
                $newMetaData = [
                    'width' => (int)$metaData['width'],
                    'height' => (int)$metaData['height'],
                    'soundcloud_html' => $metaData['soundcloud_html'],
                    'soundcloud_author_url' => $metaData['soundcloud_author_url'],
                    'soundcloud_thumbnail_url' => $metaData['soundcloud_thumbnail_url'],
                ];
                if (isset($metaData['title'])) {
                    $newMetaData['title'] = $metaData['title'];
                }
                if (isset($metaData['author'])) {
                    $newMetaData['author'] = $metaData['author'];
                }
                $this->metadataRepository->update($file->getUid(), $newMetaData);
                $this->handlePreviewImage($soundcloudHelper, $file);
                $io->success($file->getProperty('title') . '(UID: ' . $file->getUid() . ') was processed');
            }
        }
    }

    protected function handlePreviewImage(AbstractOnlineMediaHelper $onlineMediaHelper, File $file): void
    {
        $processedFiles = $this->processedFileRepository->findAllByOriginalFile($file);
        foreach ($processedFiles as $processedFile) {
            $processedFile->delete();
        }

        $videoId = $onlineMediaHelper->getOnlineMediaId($file);
        $temporaryFileName = $this->getTempFolderPath() . $file->getExtension() . '_' . md5($videoId) . '.jpg';
        if (file_exists($temporaryFileName)) {
            unlink($temporaryFileName);
        }
        $onlineMediaHelper->getPreviewImage($file);
    }

    protected function getTempFolderPath(): string
    {
        $path = Environment::getPublicPath() . '/typo3temp/assets/online_media/';
        if (!is_dir($path)) {
            GeneralUtility::mkdir_deep($path);
        }
        return $path;
    }
}
