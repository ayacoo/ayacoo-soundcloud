<?php

declare(strict_types=1);

namespace Ayacoo\AyacooSoundcloud\Helper;

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOEmbedHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Soundcloud helper class
 */
class SoundcloudHelper extends AbstractOEmbedHelper
{
    private const SOUNDCLOUD_URL = 'https://soundcloud.com/';

    protected function getOEmbedUrl($mediaId, $format = 'json')
    {
        return trim(sprintf(
            self::SOUNDCLOUD_URL . 'oembed?format=' . $format . '&url=' . self::SOUNDCLOUD_URL . '%s',
            $mediaId
        ));
    }

    public function transformUrlToFile($url, Folder $targetFolder)
    {
        $audioId = $this->getAudioId($url);
        if ($audioId === null || $audioId === '' || $audioId === '0') {
            return null;
        }

        return $this->transformMediaIdToFile($audioId, $targetFolder, $this->extension);
    }

    /**
     * Transform mediaId to File
     *
     * We override the abstract function so that we can integrate our own handling for the title field
     *
     * @param string $mediaId
     * @param string $fileExtension
     * @return File
     */
    protected function transformMediaIdToFile($mediaId, Folder $targetFolder, $fileExtension)
    {
        $file = $this->findExistingFileByOnlineMediaId($mediaId, $targetFolder, $fileExtension);
        if ($file === null) {
            $fileName = $mediaId . '.' . $fileExtension;

            $oEmbed = $this->getOEmbedData($mediaId);
            if (!empty($oEmbed['title'])) {
                $title = $this->handleSoundcloudTitle($oEmbed['title']);
                if ($title !== '' && $title !== '0') {
                    $fileName = $title . '.' . $fileExtension;
                }
            }
            $file = $this->createNewFile($targetFolder, $fileName, $mediaId);
        }
        return $file;
    }

    public function getPublicUrl(File $file, $relativeToCurrentScript = false)
    {
        // @deprecated $relativeToCurrentScript since v11, will be removed in TYPO3 v12.0
        $audioId = $this->getOnlineMediaId($file);

        return sprintf(self::SOUNDCLOUD_URL . '%s', $audioId);
    }

    public function getPreviewImage(File $file)
    {
        $properties = $file->getProperties();
        $previewImageUrl = $properties['soundcloud_thumbnail_url'] ?? '';

        $audioId = $this->getOnlineMediaId($file);
        $temporaryFileName = $this->getTempFolderPath() . 'soundcloud_' . md5($audioId) . '.jpg';

        if (!empty($previewImageUrl)) {
            $previewImage = GeneralUtility::getUrl($previewImageUrl);
            file_put_contents($temporaryFileName, $previewImage);
            GeneralUtility::fixPermissions($temporaryFileName);
            return $temporaryFileName;
        }

        return '';
    }

    /**
     * Get meta data for OnlineMedia item
     * Using the meta data from oEmbed
     *
     * @return array with metadata
     */
    public function getMetaData(File $file)
    {
        $metaData = [];

        $oEmbed = $this->getOEmbedData($this->getOnlineMediaId($file));
        if ($oEmbed) {
            $metaData['width'] = (int)$oEmbed['width'];
            // We only get the value "100%" from the oEmbed query
            // The 225 pixels come from the 16:9 format at 400 pixels
            $metaData['height'] = 225;
            if (empty($file->getProperty('title'))) {
                $metaData['title'] = $this->handleSoundcloudTitle($oEmbed['title']);
            }
            $metaData['author'] = $oEmbed['author_name'];
            $metaData['soundcloud_html'] = $oEmbed['html'];
            $metaData['soundcloud_thumbnail_url'] = $oEmbed['thumbnail_url'];
            $metaData['soundcloud_author_url'] = $oEmbed['author_url'];
        }

        return $metaData;
    }

    protected function handleSoundcloudTitle(string $title): string
    {
        return trim(mb_substr(strip_tags($title), 0, 255));
    }

    protected function getAudioId(string $url): ?string
    {
        $audioId = null;
        // Try to get the Soundcloud code from given url.
        // https://www.soundcloud.com/<username>/<path_segment>?parameter # Audio detail URL
        if (preg_match('%(?:.*)soundcloud\.com\/([a-z.\-_0-9]*)\/([a-z.\-_0-9]*)%i', $url, $match)) {
            $audioId = $match[1] . '/' . $match[2];
        }
        return $audioId;
    }
}
