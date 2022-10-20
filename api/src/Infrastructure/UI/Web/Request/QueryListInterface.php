<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Request;

interface QueryListInterface extends QueryInterface
{
    public function toArray(): array;
}
