<?php

declare(strict_types=1);

namespace Ayacoo\AyacooSoundcloud\Tests\Unit\Domain\Model;

use Ayacoo\AyacooSoundcloud\Helper\SoundcloudHelper;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOEmbedHelper;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class SoundcloudHelperTest extends UnitTestCase
{
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
                'Test'
            ],
            'Strip Tags' => [
                '<h1>Test</h1>',
                'Test'
            ],
            'Trim' => [
                'Test     ',
                'Test'
            ],
            'MaxLength' => [
                'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lore',
                'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata'
            ],
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