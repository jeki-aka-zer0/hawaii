<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV\Value;

use App\Domain\EAV\Value\Entity\ValueId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class ValueIdType extends GuidType
{
    public const FIELD_VALUE_ID = 'value_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof ValueId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ValueId
    {
        return new ValueId((string)$value);
    }

    public function getName(): string
    {
        return self::FIELD_VALUE_ID;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
