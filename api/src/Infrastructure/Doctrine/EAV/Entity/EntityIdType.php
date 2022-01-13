<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV\Entity;

use App\Domain\EAV\Entity\Entity\EntityId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;
use JetBrains\PhpStorm\Pure;

final class EntityIdType extends GuidType
{
    public const NAME = 'entity_id';

    #[Pure]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof EntityId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): EntityId
    {
        return new EntityId((string)$value);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
