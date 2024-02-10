<?php

declare(strict_types=1);

namespace Ayacoo\AyacooSoundcloud\Tests\Functional\Domain\Repository;

use Ayacoo\AyacooSoundcloud\Domain\Repository\FileRepository;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class FileRepositoryTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = ['ayacoo/ayacoo-soundcloud'];

    private FileRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->get(FileRepository::class);
    }

    /**
     * @test
     */
    public function getVideosByFileExtensionForNoRecordsReturnsEmptyResult(): void
    {
        $result = $this->subject->getVideosByFileExtension('jpg', 10);

        self::assertCount(0, $result);
    }

    /**
     * @test
     */
    public function getVideosByFileExtensionReturnsSoundcloudMedia(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Basic.csv');
        $row = $this->subject->getVideosByFileExtension('soundcloud', 10);

        self::assertCount(1, $row);
        self::assertSame(1, $row[0]['uid']);
    }

    /**
     * @test
     */
    public function getVideosByFileExtensionWithMaxResultsReturnsSoundcloudMedia(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/MaxResults.csv');
        $row = $this->subject->getVideosByFileExtension('soundcloud', 1);

        self::assertCount(1, $row);
    }

    /**
     * @test
     */
    public function getVideosByFileExtensionIgnoresMissingMedia(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/MissingSoundcloud.csv');
        $row = $this->subject->getVideosByFileExtension('soundcloud', 1);

        self::assertCount(0, $row);
    }
}
