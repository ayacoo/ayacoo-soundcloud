<?php

declare(strict_types=1);

namespace Ayacoo\AyacooSoundcloud\Tests\Unit\Tca\DisplayCond;

use Ayacoo\AyacooSoundcloud\Tca\DisplayCond\IsSoundcloud;
use PHPUnit\Framework\TestCase;

final class IsSoundcloudTest extends TestCase
{
    private IsSoundcloud $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new IsSoundcloud();
    }

    public function testReturnsFalseIfParametersAreEmpty(): void
    {
        self::assertFalse($this->subject->match([]));
    }

    public function testReturnsFalseIfRecordIsNotArray(): void
    {
        $params = ['record' => 'not-an-array'];
        self::assertFalse($this->subject->match($params));
    }

    public function testReturnsFalseIfSoundcloudHtmlIsMissing(): void
    {
        $params = ['record' => []];
        self::assertFalse($this->subject->match($params));
    }

    public function testReturnsFalseIfSoundcloudHtmlIsEmptyString(): void
    {
        $params = ['record' => ['soundcloud_html' => '']];
        self::assertFalse($this->subject->match($params));
    }

    public function testReturnsTrueIfSoundcloudHtmlHasContent(): void
    {
        $params = ['record' => ['soundcloud_html' => '<iframe>...</iframe>']];
        self::assertTrue($this->subject->match($params));
    }
}
