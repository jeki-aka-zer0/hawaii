<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV\Entity;

use App\Domain\EAV\Entity\Entity\EntityId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class EntityIdType extends GuidType
{
    public const FIELD_ENTITY_ID = 'entity_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof EntityId ? $value->getVal() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): EntityId
    {
        return new EntityId((string)$value);
    }

    public function getName(): string
    {
        return self::FIELD_ENTITY_ID;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
