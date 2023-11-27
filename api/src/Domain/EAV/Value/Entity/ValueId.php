<?php

declare(strict_types=1);

namespace App\Domain\EAV\Value\Entity;

use App\Domain\Shared\Entity\AbstractId;
use App\Infrastructure\Doctrine\EAV\Value\ValueIdType;

final class ValueId extends AbstractId
{
    public function getField(): string
    {
        return ValueIdType::FIELD_VALUE_ID;
    }
}
