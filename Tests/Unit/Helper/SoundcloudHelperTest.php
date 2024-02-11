<?php

declare(strict_types=1);

namespace Ayacoo\AyacooSoundcloud\Tests\Unit\Domain\Model;

use Ayacoo\AyacooSoundcloud\Helper\SoundcloudHelper;
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
    public function isAbstractEntity(): void
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
                'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lore',
                'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata',
            ],
        ];
    }

    /**
     * @test
     * @return void
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
     * @return void
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

    private function buildReflectionForProtectedFunction(string $methodName, array $params)
    {
        $reflectionCalendar = new \ReflectionClass($this->subject);
        $method = $reflectionCalendar->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($this->subject, $params);
    }
}
