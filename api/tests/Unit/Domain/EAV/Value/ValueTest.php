<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Value;

use App\Application\EAV\Builder;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Value\Entity\Value;
use App\Domain\EAV\Value\Entity\ValueId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ValueTest extends TestCase
{
    public function testUpdateValue(): void
    {
        $attribute = Builder::buildAttr();
        $value = Builder::buildValue();
        $newValue = Builder::getRandomValue($attribute);

        $value->updateValue($newValue);

        self::assertEquals($newValue, $value->value);
    }

    public function equalDataProvider(): array
    {
        $entity = Builder::buildEntity($entityId = EntityId::generate());
        $attribute = Builder::buildAttr($attrId = AttributeId::generate());
        $value = Builder::buildValue($entity, $attribute);

        return [
            'same by all attributes' => [
                'value' => $value,
                'expected' => true,
                'valueId' => $value->valueId,
                'entityId' => $entityId,
                'attrId' => $attrId,
            ],
            'same by valueId and entityId' => [
                'value' => $value,
                'expected' => true,
                'valueId' => $value->valueId,
                'entityId' => $entityId,
                'attrId' => null,
            ],
            'same by valueId and attrId' => [
                'value' => $value,
                'expected' => true,
                'valueId' => $value->valueId,
                'entityId' => null,
                'attrId' => $attrId,
            ],
            'same by entityId and attrId' => [
                'value' => $value,
                'expected' => true,
                'valueId' => null,
                'entityId' => $entityId,
                'attrId' => $attrId,
            ],
            'same by value id' => [
                'value' => $value,
                'expected' => true,
                'valueId' => $value->valueId,
                'entityId' => null,
                'attrId' => null,
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
        ?AttributeId $attrId = null,
    ): void {
        self::assertEquals($expected, $value->isEqual($valueId, $entityId, $attrId));
    }

    public function testIsEqualShouldFailWithoutArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Builder::buildValue()->isEqual();
    }
}
