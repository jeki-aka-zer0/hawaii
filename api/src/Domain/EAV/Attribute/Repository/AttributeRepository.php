<?php

declare(strict_types=1);

namespace App\Domain\EAV\Attribute\Repository;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\Shared\Repository\EntityNotFoundException;

interface AttributeRepository
{
    /**
     * @throws EntityNotFoundException
     */
    public function get(AttributeId $attributeId): Attribute;

    public function hasByName(string $name): bool;

    public function add(Attribute $attribute): void;
}
