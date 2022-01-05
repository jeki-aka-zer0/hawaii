<?php

declare(strict_types=1);

namespace App\Domain\EAV\Attribute\Repository;

use App\Domain\EAV\Attribute\Entity\Attribute;

interface AttributeRepository
{
    public function hasByName(string $name): bool;

    public function add(Attribute $attribute): void;
}
