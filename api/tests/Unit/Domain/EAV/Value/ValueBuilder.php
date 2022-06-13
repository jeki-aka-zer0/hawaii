<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Value;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Value\Entity\Value;
use App\Domain\EAV\Value\Entity\ValueId;

final class ValueBuilder
{
    private const TEST_STRING_VALUE = 'test value';
    private const TEST_INT_VALUE = 1;

    public function build(Entity $entity, Attribute $attribute): Value
    {
        $value = match ($attribute->type) {
            AttributeType::String => self::TEST_STRING_VALUE,
            AttributeType::Int => self::TEST_INT_VALUE,
        };

        return new Value(ValueId::generate(), $entity, $attribute, $value);
    }
}
