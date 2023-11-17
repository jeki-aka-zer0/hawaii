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
    public function testUpdateVal(): void
    {
        $attr = Builder::buildAttr();
        $val = Builder::buildVal();
        $newVal = Builder::getRandomVal($attr);

        $val->updateVal($newVal);

        self::assertEquals($newVal, $val->value);
    }

    public function equalDataProvider(): array
    {
        $entity = Builder::buildEntity($entityId = EntityId::generate());
        $attr = Builder::buildAttr($attrId = AttributeId::generate());
        $val = Builder::buildVal($entity, $attr);

        return [
            'same by all attributes' => [
                'val' => $val,
                'expected' => true,
                'valId' => $val->valueId,
                'entityId' => $entityId,
                'attrId' => $attrId,
            ],
            'same by valId and entityId' => [
                'val' => $val,
                'expected' => true,
                'valId' => $val->valueId,
                'entityId' => $entityId,
                'attrId' => null,
            ],
            'same by valId and attrId' => [
                'val' => $val,
                'expected' => true,
                'valId' => $val->valueId,
                'entityId' => null,
                'attrId' => $attrId,
            ],
            'same by entityId and attrId' => [
                'val' => $val,
                'expected' => true,
                'valId' => null,
                'entityId' => $entityId,
                'attrId' => $attrId,
            ],
            'same by val id' => [
                'val' => $val,
                'expected' => true,
                'valId' => $val->valueId,
                'entityId' => null,
                'attrId' => null,
            ],
        ];
    }

    /**
     * @dataProvider equalDataProvider
     */
    public function testIsEqual(
        Value $val,
        bool $expected,
        ?ValueId $valId = null,
        ?EntityId $entityId = null,
        ?AttributeId $attrId = null,
    ): void {
        self::assertEquals($expected, $val->isEqual($valId, $entityId, $attrId));
    }

    public function testIsEqualShouldFailWithoutArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Builder::buildVal()->isEqual();
    }
}
