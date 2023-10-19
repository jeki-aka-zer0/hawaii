<?php

declare(strict_types=1);

namespace App\Application\Shared;

final readonly class Field
{
    public function __construct(
        public string $entity,
        public string $field,
        public mixed $value,
    ) {
    }
}