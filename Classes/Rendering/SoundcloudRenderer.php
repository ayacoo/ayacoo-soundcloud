<?php

declare(strict_types=1);

namespace Ayacoo\AyacooSoundcloud\Rendering;

use Ayacoo\AyacooSoundcloud\Event\ModifySoundcloudOutputEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperInterface;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\Resource\Rendering\FileRendererInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;

/**
 * Soundcloud renderer class
 */
class SoundcloudRenderer implements FileRendererInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ConfigurationManager     $configurationManager
    )
    {

    }

    /**
     * @var OnlineMediaHelperInterface|false
     */
    protected $onlineMediaHelper;

    /**
     * Returns the priority of the renderer
     * This way it is possible to define/overrule a renderer
     * for a specific file type/context.
     * For example create a video renderer for a certain storage/driver type.
     * Should be between 1 and 100, 100 is more important than 1
     *
     * @return int
     */
    public function getPriority()
    {
        return 1;
    }

    /**
     * Check if given File(Reference) can be rendered
     *
     * @param FileInterface $file File of FileReference to render
     * @return bool
     */
    public function canRender(FileInterface $file)
    {
        return ($file->getMimeType() === 'audio/soundcloud' || $file->getExtension() === 'soundcloud') && $this->getOnlineMediaHelper($file) !== false;
    }

    public function render(FileInterface $file, $width, $height, array $options = [], $usedPathsRelativeToCurrentScript = false)
    {
        $output = $file->getProperty('soundcloud_html') ?? '';
        if ($this->getPrivacySetting()) {
            $output = str_replace('src', 'data-name="iframe-soundcloud" data-src', $output);
        }

        $modifySoundcloudOutputEvent = $this->eventDispatcher->dispatch(
            new ModifySoundcloudOutputEvent($output)
        );
        return $modifySoundcloudOutputEvent->getOutput();
    }

    /**
     * Get online media helper
     *
     * @param FileInterface $file
     * @return false|OnlineMediaHelperInterface
     */
    protected function getOnlineMediaHelper(FileInterface $file)
    {
        if ($this->onlineMediaHelper === null) {
            $orgFile = $file;
            if ($orgFile instanceof FileReference) {
                $orgFile = $orgFile->getOriginalFile();
            }
            if ($orgFile instanceof File) {
                $this->onlineMediaHelper = GeneralUtility::makeInstance(OnlineMediaHelperRegistry::class)->getOnlineMediaHelper($orgFile);
            } else {
                $this->onlineMediaHelper = false;
            }
        }
        return $this->onlineMediaHelper;
    }

    /**
     * @return bool
     */
    protected function getPrivacySetting(): bool
    {
        try {
            $privacy = false;
            $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            );
            if (isset($extbaseFrameworkConfiguration['plugin.']['tx_ayacoosoundcloud.'])) {
                $privacy = (bool)$extbaseFrameworkConfiguration['plugin.']['tx_ayacoosoundcloud.']['settings.']['privacy'] ?? false;
            }
            return $privacy;
        } catch (InvalidConfigurationTypeException $e) {
            return false;
        }
    }
}
