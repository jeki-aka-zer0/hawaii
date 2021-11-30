<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Model;

use App\Domain\EAV\Entity\EntityId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;
use JetBrains\PhpStorm\Pure;

final class DoctrineEntityIdType extends GuidType
{
    public const NAME = 'entity_id';

    #[Pure]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof EntityId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?EntityId
    {
        return empty($value) ? null : new EntityId((string)$value);
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
