<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Shared\Util;

use App\Domain\Shared\Util\Str;
use PHPUnit\Framework\TestCase;

final class StrTest extends TestCase
{
    public function trunc(): array
    {
        return [
            'by default a string is truncated to 15 chars and three dots are added at the end' => [
                'str' => 'This is too long string',
                'expected' => 'This is too lon...'
            ],
            'string remains as is if it is too short' => [
                'str' => 'Short string',
                'expected' => 'Short string'
            ],
            'custom length and ending' => [
                'str' => 'This string is long enough',
                'expected' => 'This!',
                'maxLen' => 4,
                'ending' => '!'
            ],
            'removes spaces at the right' => [
                'str' => 'This        should be cut and trimmed',
                'expected' => 'This*',
                'maxLen' => 10,
                'ending' => '*'
            ],
        ];
    }

    /**
     * @dataProvider trunc
     */
    public function testTrunc(string $str, string $expected, int $maxLen = null, string $ending = null): void
    {
        $sut = new Str($str);

        $actual = null === $maxLen
            ? $sut->trunc()
            : $sut->trunc($maxLen, $ending);

        self::assertEquals($expected, (string)$actual);
    }

    public function testLow(): void
    {
        $sut = new Str('This LIne Is In DiffErent RegistErs');

        $actual = $sut->low();

        self::assertEquals('this line is in different registers', (string)$actual);
    }

    public function testTrim(): void
    {
        $sut = new Str(' test ');

        $actual = $sut->trim();

        self::assertEquals('test', (string)$actual);
    }

    public function testUpFirst(): void
    {
        $sut = new Str('test');

        $actual = $sut->upFirst();

        self::assertEquals('Test', (string)$actual);
    }

    public function testHumanize(): void
    {
        $sut = new Str('member_identity');

        $actual = $sut->humanize();

        self::assertEquals('member identity', (string)$actual);
    }
}