<?php

declare(strict_types=1);

namespace App\Domain\Shared\Repository;

use App\Application\Shared\Field;
use App\Domain\Shared\Util\Str;
use DomainException;

final class FieldException extends DomainException
{
    private string $field;

    public static function build(string $field, string $message): self
    {
        $e = new self($message);
        $e->field = $field;

        return $e;
    }

    public static function alreadyExists(Field $field): self
    {
        return self::build(
            $field->field,
            sprintf(
                '%s with the %s "%s" already exists',
                (new Str($field->entity))->low()->upFirst(),
                (new Str($field->field))->humanize(),
                (new Str($field->val))->trim()->trunc(),
            )
        );
    }

    public function getField(): string
    {
        return $this->field;
    }
}
