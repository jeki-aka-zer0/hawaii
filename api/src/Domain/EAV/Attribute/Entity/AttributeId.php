<?php

declare(strict_types=1);

namespace App\Domain\EAV\Attribute\Entity;

use App\Domain\Shared\Entity\AbstractId;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeIdType;

final class AttributeId extends AbstractId
{
    public function getField(): string
    {
        return AttributeIdType::FIELD_ATTR_ID;
    }
}
