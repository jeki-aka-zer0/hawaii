<?php

declare(strict_types=1);

namespace App\Domain\Shared\Util;

final class Errors
{
    public static function alreadyExists(string $objectType, string $fieldLabel, string $fieldValue): string
    {
        return sprintf(
            'An %s with the "%s" "%s" already exists',
            (new Str($objectType))->low(),
            $fieldLabel,
            (new Str($fieldValue))->trim()->low()->trunc(),
        );
    }
}