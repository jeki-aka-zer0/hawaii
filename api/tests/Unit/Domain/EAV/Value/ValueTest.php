<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Value;

use App\Application\EAV\Builder;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Value\Entity\Value;
use App\Domain\EAV\Value\Entity\ValueId;
use App\Tests\Unit\Domain\EAV\Entity\EntityBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ValueTest extends TestCase
{
    public function testUpdateValue(): void
    {
        $attribute = Builder::buildAttribute();
        $value = Builder::buildValue();
        $newValue = Builder::getRandomValue($attribute);

        $value->updateValue($newValue);

        self::assertEquals($newValue, $value->value);
    }

    public function equalDataProvider(): array
    {
        $entity = (new EntityBuilder())->build($entityId = EntityId::generate());
        $attribute = Builder::buildAttribute($attributeId = AttributeId::generate());
        $value = Builder::buildValue($entity, $attribute);

        return [
            'same by all attributes' => [
                'value' => $value,
                'expected' => true,
                'valueId' => $value->valueId,
                'entityId' => $entityId,
                'attributeId' => $attributeId,
            ],
            'same by valueId and entityId' => [
                'value' => $value,
                'expected' => true,
                'valueId' => $value->valueId,
                'entityId' => $entityId,
                'attributeId' => null,
            ],
            'same by valueId and attributeId' => [
                'value' => $value,
                'expected' => true,
                'valueId' => $value->valueId,
                'entityId' => null,
                'attributeId' => $attributeId,
            ],
            'same by entityId and attributeId' => [
                'value' => $value,
                'expected' => true,
                'valueId' => null,
                'entityId' => $entityId,
                'attributeId' => $attributeId,
            ],
            'same by value id' => [
                'value' => $value,
                'expected' => true,
                'valueId' => $value->valueId,
                'entityId' => null,
                'attributeId' => null,
            ],
        ];
    }

    /**
     * @dataProvider equalDataProvider
     */
    public function testIsEqual(
        Value $value,
        bool $expected,
        ?ValueId $valueId = null,
        ?EntityId $entityId = null,
        ?AttributeId $attributeId = null,
    ): void {
        self::assertEquals($expected, $value->isEqual($valueId, $entityId, $attributeId));
    }

    public function testIsEqualShouldFailWithoutArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Builder::buildValue()->isEqual();
    }
}
