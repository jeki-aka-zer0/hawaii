<?php

declare(strict_types=1);

namespace App\Domain\Shared\Repository;

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

    public function getField(): string
    {
        return $this->field;
    }
}
