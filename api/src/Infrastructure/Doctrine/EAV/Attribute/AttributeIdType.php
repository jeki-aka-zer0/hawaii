<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV\Attribute;

use App\Domain\EAV\Attribute\Entity\AttributeId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class AttributeIdType extends GuidType
{
    public const FIELD_ATTR_ID = 'attribute_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof AttributeId ? $value->getVal() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): AttributeId
    {
        return new AttributeId((string)$value);
    }

    public function getName(): string
    {
        return self::FIELD_ATTR_ID;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
