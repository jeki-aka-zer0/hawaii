<?php

declare(strict_types=1);

namespace App\Domain\EAV\Entity\Entity;

use App\Domain\Shared\Entity\AbstractId;
use App\Infrastructure\Doctrine\EAV\Entity\EntityIdType;

final class EntityId extends AbstractId
{
    public function getField(): string
    {
        return EntityIdType::FIELD_ENTITY_ID;
    }
}
