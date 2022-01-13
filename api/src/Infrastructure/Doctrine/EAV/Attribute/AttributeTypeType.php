<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV\Attribute;

use App\Domain\EAV\Attribute\Entity\AttributeType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\SmallIntType;
use RuntimeException;

final class AttributeTypeType extends SmallIntType
{
    public const NAME = 'attribute_type';

    public const STRING = 0;
    public const INT = 1;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): int
    {
        /** @var AttributeType|mixed $value */
        return match ($value) {
            AttributeType::String => self::STRING,
            AttributeType::Int => self::INT,
            default => throw new RuntimeException('Unexpected attribute type'),
        };
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): AttributeType
    {
        return match ($value) {
            self::STRING => AttributeType::String,
            self::INT => AttributeType::Int,
            default => throw new RuntimeException('Unexpected attribute type'),
        };
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
