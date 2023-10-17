<?php

declare(strict_types=1);

namespace App\Domain\Shared\Util;

final class Err
{
    public static function alreadyExists(string $objectType, string $fieldLabel, string $fieldValue): string
    {
        return sprintf(
            '%s with the %s "%s" already exists',
            (new Str($objectType))->low()->upFirst(),
            (new Str($fieldLabel))->low(),
            (new Str($fieldValue))->trim()->trunc(),
        );
    }
}