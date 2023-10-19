<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Repository;

use App\Application\Shared\Field;
use App\Domain\Shared\Repository\FieldException;
use PHPUnit\Framework\TestCase;

final class FieldExceptionTest extends TestCase
{
    public function alreadyExists(): array
    {
        return [
            'ok' => [
                'field' => new Field('uSeR', 'user_name', 'Evgeniy'),
                'expected' => 'User with the user name "Evgeniy" already exists',
            ],
            'title trimmed' => [
                'field' => new Field('article', 'title', 'Evolution of Feature: MVP — Beta — Feature Freeze'),
                'expected' => 'Article with the title "Evolution of Fe..." already exists',
            ],
        ];
    }

    /**
     * @dataProvider alreadyExists
     */
    public function testAlreadyExists(Field $field, string $expected): void
    {
        $actual = FieldException::alreadyExists($field);

        self::assertEquals($expected, $actual->getMessage());
    }
}