<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Shared\Util;

use App\Domain\Shared\Util\Err;
use PHPUnit\Framework\TestCase;

final class ErrTest extends TestCase
{
    public function alreadyExists(): array
    {
        return [
            'ok' => [
                'objectType' => 'bOoK',
                'fieldLabel' => 'TITLE',
                'fieldValue' => ' Bible ',
                'expected' => 'Book with the title "Bible" already exists',
            ],
            'title trimmed' => [
                'objectType' => 'article',
                'fieldLabel' => 'name',
                'fieldValue' => 'Evolution of Feature: MVP — Beta — Feature Freeze',
                'expected' => 'Article with the name "Evolution of Fe..." already exists',
            ],
        ];
    }

    /**
     * @dataProvider alreadyExists
     */
    public function testAlreadyExists(
        string $objectType,
        string $fieldLabel,
        string $fieldValue,
        string $expected
    ): void {
        $actual = Err::alreadyExists($objectType, $fieldLabel, $fieldValue);

        self::assertEquals($expected, $actual);
    }
}