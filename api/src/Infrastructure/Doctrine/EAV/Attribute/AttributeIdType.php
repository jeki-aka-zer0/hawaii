<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV\Attribute;

use App\Domain\EAV\Attribute\Entity\AttributeId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;
use JetBrains\PhpStorm\Pure;

final class AttributeIdType extends GuidType
{
    public const FIELD_ATTRIBUTE_ID = 'attribute_id';

    #[Pure]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof AttributeId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): AttributeId
    {
        return new AttributeId((string)$value);
    }

    public function getName(): string
    {
        return self::FIELD_ATTRIBUTE_ID;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
