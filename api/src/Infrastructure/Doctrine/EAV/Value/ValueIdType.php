<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV\Value;

use App\Domain\EAV\Value\Entity\ValueId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;
use JetBrains\PhpStorm\Pure;

final class ValueIdType extends GuidType
{
    public const NAME = 'value_id';

    #[Pure]
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
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
