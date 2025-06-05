<?php

declare(strict_types=1);

namespace Ayacoo\AyacooSoundcloud\Tests\Unit\Domain\Repository;

use Ayacoo\AyacooSoundcloud\Domain\Repository\FileRepository;
use Doctrine\DBAL\Platforms\AbstractPlatform as DoctrineAbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class FileRepositoryTest extends UnitTestCase
{
    private FileRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new FileRepository();
    }

    #[Test]
    #[DataProvider('platformIdentifierDataProvider')]
    public function getPlatformIdentifierReturnsExpectedIdentifier(DoctrineAbstractPlatform $platform, string $expected): void
    {
        $params = [$platform];
        $methodName = 'getPlatformIdentifier';
        $result = $this->buildReflectionForProtectedFunction($methodName, $params);

        self::assertSame($expected, $result);
    }

    public static function platformIdentifierDataProvider(): array
    {
        return [
            'MariaDB platform' => [new MariaDBPlatform(), 'mysql'],
            'MySQL platform' => [new MySQLPlatform(), 'mysql'],
            'PostgreSQL platform' => [new PostgreSQLPlatform(), 'postgresql'],
            'SQLite platform' => [new SQLitePlatform(), 'sqlite'],
        ];
    }

    #[Test]
    public function getPlatformIdentifierWithUnsupportedPlatformThrowsRuntimeException(): void
    {
        $platformMock = self::createMock(DoctrineAbstractPlatform::class);

        $this->expectException(\RuntimeException::class);

        $this->buildReflectionForProtectedFunction('getPlatformIdentifier', [$platformMock]);
    }

    private function buildReflectionForProtectedFunction(string $methodName, array $params)
    {
        $reflection = new \ReflectionClass($this->subject);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($this->subject, $params);
    }
}
