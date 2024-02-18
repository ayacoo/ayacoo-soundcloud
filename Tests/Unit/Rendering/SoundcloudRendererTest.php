<?php

declare(strict_types=1);

namespace Ayacoo\AyacooSoundcloud\Tests\Unit\Rendering;

use Ayacoo\AyacooSoundcloud\Helper\SoundcloudHelper;
use Ayacoo\AyacooSoundcloud\Rendering\SoundcloudRenderer;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Rendering\FileRendererInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class SoundcloudRendererTest extends UnitTestCase
{
    private SoundcloudRenderer $subject;

    protected bool $resetSingletonInstances = true;

    protected function setUp(): void
    {
        parent::setUp();

        $eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        //$eventDispatcherMock->expects(self::atLeastOnce())->method('dispatch')->with(self::anything())->willReturnArgument(0);

        $configurationManager = $this->getMockBuilder(ConfigurationManager::class)
            ->onlyMethods(['getConfiguration'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->subject = new SoundcloudRenderer($eventDispatcherMock, $configurationManager);
    }

    /**
     * @test
     */
    public function hasFileRendererInterface(): void
    {
        self::assertInstanceOf(FileRendererInterface::class, $this->subject);
    }

    /**
     * @test
     */
    public function canRenderWithMatchingMimeTypeReturnsTrue(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers']['soundcloud'] = SoundcloudHelper::class;

        $fileResourceMock = $this->createMock(File::class);
        $fileResourceMock->expects(self::any())->method('getMimeType')->willReturn('audio/soundcloud');
        $fileResourceMock->expects(self::any())->method('getExtension')->willReturn('soundcloud');

        $result = $this->subject->canRender($fileResourceMock);
        self::assertTrue($result);
    }

    /**
     * @test
     */
    public function canRenderWithMatchingMimeTypeReturnsFalse(): void
    {
        $fileResourceMock = $this->createMock(File::class);
        $fileResourceMock->expects(self::any())->method('getMimeType')->willReturn('video/youtube');
        $fileResourceMock->expects(self::any())->method('getExtension')->willReturn('youtube');

        $result = $this->subject->canRender($fileResourceMock);
        self::assertFalse($result);
    }

    private function buildReflectionForProtectedFunction(string $methodName, array $params)
    {
        $reflectionCalendar = new \ReflectionClass($this->subject);
        $method = $reflectionCalendar->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($this->subject, $params);
    }
}
