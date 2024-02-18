<?php

declare(strict_types=1);

namespace Ayacoo\AyacooSoundcloud\Tests\Unit\Helper;

use Ayacoo\AyacooSoundcloud\Helper\SoundcloudHelper;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOEmbedHelper;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class SoundcloudHelperTest extends UnitTestCase
{
    private const SOUNDCLOUD_URL = 'https://soundcloud.com/';

    private SoundcloudHelper $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new SoundcloudHelper('soundcloud');
    }

    /**
     * @test
     */
    public function isAbstractOEmbedHelper(): void
    {
        self::assertInstanceOf(AbstractOEmbedHelper::class, $this->subject);
    }

    /**
     * @test
     * @dataProvider handleSoundcloudTitleDataProvider
     */
    public function handleSoundcloudTitleReturnsFilteredTitle(string $input, string $expected)
    {
        $params = [$input];
        $methodName = 'handleSoundcloudTitle';
        $result = $this->buildReflectionForProtectedFunction($methodName, $params);

        self::assertEquals($expected, $result);
    }

    public static function handleSoundcloudTitleDataProvider(): array
    {
        $maxLengthText = self::generateRandomString(300);
        return [
            'No filter needed' => [
                'Test',
                'Test',
            ],
            'Strip Tags' => [
                '<h1>Test</h1>',
                'Test',
            ],
            'Trim' => [
                'Test     ',
                'Test',
            ],
            'MaxLength' => [
                $maxLengthText,
                substr($maxLengthText, 0, 255),
            ],
        ];
    }

    /**
     * @test
     */
    public function getOEmbedUrlWithJsonFormat(): void
    {
        $mediaId = 'user/song';
        $expectedUrl = self::SOUNDCLOUD_URL . 'oembed?format=json&url=' . self::SOUNDCLOUD_URL . $mediaId;

        $params = [$mediaId];
        $methodName = 'getOEmbedUrl';
        $actualUrl = $this->buildReflectionForProtectedFunction($methodName, $params);

        self::assertEquals($expectedUrl, $actualUrl);
    }

    /**
     * @test
     */
    public function getOEmbedUrlWithXmlFormat(): void
    {
        $mediaId = 'user/another-song';
        $format = 'xml';
        $expectedUrl = self::SOUNDCLOUD_URL . 'oembed?format=' . $format . '&url=' . self::SOUNDCLOUD_URL . $mediaId;

        $params = [$mediaId, $format];
        $methodName = 'getOEmbedUrl';
        $actualUrl = $this->buildReflectionForProtectedFunction($methodName, $params);

        self::assertEquals($expectedUrl, $actualUrl);
    }

    /**
     * @test
     * @dataProvider getAudioIdDataProvider
     */
    public function getAudioIdWithValidUrlReturnsAudioIdOrNull(string $url, mixed $expectedAudioId)
    {
        $params = [$url];
        $methodName = 'getAudioId';
        $actualAudioId = $this->buildReflectionForProtectedFunction($methodName, $params);

        self::assertSame($expectedAudioId, $actualAudioId);
    }

    public static function getAudioIdDataProvider(): array
    {
        return [
            ['https://www.soundcloud.com/username/songname', 'username/songname'],
            ['https://www.soundcloud.com/username/songname?playlist', 'username/songname'],
            ['https://soundcloud.com/user_name/song_name', 'user_name/song_name'],
            ['https://soundcloud.com/user-name/track123', 'user-name/track123'],
            ['https://soundcloud.com/', null],
            ['https://google.com', null],
        ];
    }

    /**
     * @test
     */
    public function getOEmbedDataWithEmbedDataReturnsOptimizedArray()
    {
        $fileResourceMock = $this->createMock(File::class);
        $fileResourceMock->expects(self::any())->method('getMimeType')->willReturn('audio/soundcloud');

        /** @var SoundcloudHelper|\PHPUnit\Framework\MockObject\MockObject $soundcloudHelperMock */
        $soundcloudHelperMock = $this->getMockBuilder(SoundcloudHelper::class)
            ->setConstructorArgs(['soundcloud'])
            ->onlyMethods(['getOEmbedData', 'getOnlineMediaId'])
            ->getMock();

        $title = 'Alan Walker, Dash Berlin, Vikkstar - Better Off (Alone, Pt. III) by Alan Walker';
        $author = 'Dash Berlin';
        $oEmbedData = [
            'width' => 100,
            'height' => '100%',
            'title' => $title,
            'author_name' => $author,
        ];

        $soundcloudHelperMock->expects(self::any())
            ->method('getOnlineMediaId')
            ->with($fileResourceMock)
            ->willReturn('alanwalker/better-off-alone-pt-iii-1');
        $soundcloudHelperMock->expects(self::any())->method('getOEmbedData')->willReturn($oEmbedData);

        $expected = [
            'width' => 100,
            'height' => 225,
            'title' => $title,
            'author' => $author,
            'soundcloud_html' => '',
            'soundcloud_thumbnail_url' => '',
            'soundcloud_author_url' => '',
        ];

        $result = $soundcloudHelperMock->getMetaData($fileResourceMock);

        self::assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function getOEmbedDataWithoutEmbedDataReturnsEmptyArray()
    {
        $fileResourceMock = $this->createMock(File::class);
        $fileResourceMock->expects(self::any())->method('getMimeType')->willReturn('audio/soundcloud');

        /** @var SoundcloudHelper|\PHPUnit\Framework\MockObject\MockObject $soundcloudHelperMock */
        $soundcloudHelperMock = $this->getMockBuilder(SoundcloudHelper::class)
            ->setConstructorArgs(['soundcloud'])
            ->onlyMethods(['getOEmbedData', 'getOnlineMediaId'])
            ->getMock();

        $soundcloudHelperMock->expects(self::any())
            ->method('getOnlineMediaId')
            ->with($fileResourceMock)
            ->willReturn('alanwalker/better-off-alone-pt-iii-1');
        $soundcloudHelperMock->expects(self::any())->method('getOEmbedData')->willReturn(null);

        $result = $soundcloudHelperMock->getMetaData($fileResourceMock);
        self::assertSame([], $result);
    }

    /**
     * @test
     */
    public function getPublicUrlReturnsPublicUrl()
    {
        $fileResourceMock = $this->createMock(File::class);
        $fileResourceMock->expects(self::any())->method('getMimeType')->willReturn('audio/soundcloud');

        /** @var SoundcloudHelper|\PHPUnit\Framework\MockObject\MockObject $soundcloudHelperMock */
        $soundcloudHelperMock = $this->getMockBuilder(SoundcloudHelper::class)
            ->setConstructorArgs(['soundcloud'])
            ->onlyMethods(['getOnlineMediaId'])
            ->getMock();

        $soundcloudHelperMock->expects(self::any())
            ->method('getOnlineMediaId')
            ->with($fileResourceMock)
            ->willReturn('alanwalker/better-off-alone-pt-iii-1');

        $expected = 'https://soundcloud.com/alanwalker/better-off-alone-pt-iii-1';

        $result = $soundcloudHelperMock->getPublicUrl($fileResourceMock);
        self::assertSame($expected, $result);
    }

    private function buildReflectionForProtectedFunction(string $methodName, array $params)
    {
        $reflectionCalendar = new \ReflectionClass($this->subject);
        $method = $reflectionCalendar->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($this->subject, $params);
    }

    private static function generateRandomString(int $size): string
    {
        $bytes = random_bytes($size);
        $randomString = bin2hex($bytes);
        return substr($randomString, 0, $size);
    }
}
