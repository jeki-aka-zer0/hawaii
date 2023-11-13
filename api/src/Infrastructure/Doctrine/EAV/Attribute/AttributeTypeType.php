<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV\Attribute;

use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\Shared\Util\Str;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\SmallIntType;
use InvalidArgumentException;

final class AttributeTypeType extends SmallIntType
{
    public const NAME = 'attribute_type';

    public const STRING = 0;
    public const INT = 1;

    /**
     * @param AttributeType|mixed $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): int
    {
        return match ($value) {
            AttributeType::String => self::STRING,
            AttributeType::Int => self::INT,
            default => throw new InvalidArgumentException(sprintf('Unexpected %s', self::getNameHumanize())),
        };
    }

    /**
     * @param int|mixed $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): AttributeType
    {
        return match ($value) {
            self::STRING => AttributeType::String,
            self::INT => AttributeType::Int,
            default => throw new InvalidArgumentException(sprintf('Unexpected %s "%d" in db', self::getNameHumanize(), $value)),
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

    private static function getNameHumanize(): Str
    {
        return (new Str(self::NAME))->humanize();
    }
}
