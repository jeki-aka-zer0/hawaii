<?php

declare(strict_types=1);

namespace App\Domain\EAV\Value\Repository;

use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Value\Entity\Value;

interface ValueRepository
{
    public function findByEntityAndAttribute(EntityId $entityId, AttributeId $attributeId): ?Value;

    public function add(Value $value): void;
}
