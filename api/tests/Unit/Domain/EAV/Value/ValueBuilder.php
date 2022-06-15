<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Value;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Value\Entity\Value;
use App\Domain\EAV\Value\Entity\ValueId;
use App\Tests\Unit\Domain\EAV\Attribute\AttributeBuilder;
use App\Tests\Unit\Domain\EAV\Entity\EntityBuilder;

final class ValueBuilder
{
    public function build(Entity $entity = null, Attribute $attribute = null): Value
    {
        return new Value(
            ValueId::generate(),
            $entity ?? (new EntityBuilder())->build(EntityId::generate()),
            $attribute ??= (new AttributeBuilder())->build(AttributeId::generate()),
            self::generateRandomValueByAttribute($attribute)
        );
    }

    public static function generateRandomValueByAttribute(Attribute $attribute): string|int
    {
        return match ($attribute->type) {
            AttributeType::String => self::generateRandomString(),
            AttributeType::Int => self::generateRandomInt(),
        };
    }

    public static function generateRandomString(): string
    {
        return sha1(microtime().self::generateRandomInt());
    }

    public static function generateRandomInt(): int
    {
        return random_int(PHP_INT_MIN, PHP_INT_MAX);
    }
}
