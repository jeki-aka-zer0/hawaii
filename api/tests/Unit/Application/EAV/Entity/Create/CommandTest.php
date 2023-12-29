<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\EAV\Entity\Create;

use App\Application\EAV\Builder;
use App\Application\EAV\Entity\Create\Command;
use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Value\Entity\Value;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeIdType;
use PHPUnit\Framework\TestCase;

final class CommandTest extends TestCase
{
    public function testGetAttrsValMap(): void
    {
        $attrIdShouldBeProcessed = AttributeId::generate()->getVal();
        $valShouldBeIgnored = Builder::getRandVal();
        $valShouldBeProcessed = Builder::getRandVal(exclude: $valShouldBeIgnored);
        $intValShouldBeProcessed = Builder::getRandIntVal();
        $attrNameShouldBeProcessed = Builder::getRandAttrName();
        $attrNameShouldBeProcessedInt = Builder::getRandAttrName(exclude: [$attrNameShouldBeProcessed]);
        $SUT = Command::build('', attrsVal: [
            'should be rewritten by the next value of the same attribute name' => [
                Attribute::FIELD_NAME => $attrNameShouldBeProcessed,
                Value::FIELD_VALUE => $valShouldBeIgnored,
            ],
            'should be processed with all fields, attribute name should be trimmed, attribute_id should be trimmed and lower cased' => [
                Attribute::FIELD_NAME => " $attrNameShouldBeProcessed ",
                Value::FIELD_VALUE => $valShouldBeProcessed,
                AttributeIdType::FIELD_ATTR_ID => ' '.strtoupper($attrIdShouldBeProcessed).' ',
            ],
            'should be processed without attribute_id and numeric value should be converted to int and trimmed' => [
                Attribute::FIELD_NAME => $attrNameShouldBeProcessedInt,
                Value::FIELD_VALUE => " $intValShouldBeProcessed ",
            ],
            'should be ignored because the attribute name is an empty string' => [
                Attribute::FIELD_NAME => '',
                Value::FIELD_VALUE => $valShouldBeIgnored,
            ],
            'should be ignored because the attribute name is false' => [
                Attribute::FIELD_NAME => false,
                Value::FIELD_VALUE => $valShouldBeIgnored,
            ],
            'should be ignored because the attribute name is null' => [
                Attribute::FIELD_NAME => null,
                Value::FIELD_VALUE => $valShouldBeIgnored,
            ],
            'should be ignored because the attribute name is not set' => [
                Value::FIELD_VALUE => $valShouldBeIgnored,
            ],
            'should be ignored because the value is an empty string' => [
                Attribute::FIELD_NAME => $attrNameShouldBeProcessed,
                Value::FIELD_VALUE => '',
            ],
            'should be ignored because the value is false' => [
                Attribute::FIELD_NAME => $attrNameShouldBeProcessed,
                Value::FIELD_VALUE => false,
            ],
            'should be ignored because the value is null' => [
                Attribute::FIELD_NAME => $attrNameShouldBeProcessed,
                Value::FIELD_VALUE => null,
            ],
            'should be ignored because the value is not set' => [
                Attribute::FIELD_NAME => $attrNameShouldBeProcessed,
            ],
        ]);

        $attrVal = $SUT->getAttrsValMap();

        self::assertEquals([
            $attrNameShouldBeProcessed => [
                Value::FIELD_VALUE => $valShouldBeProcessed,
                AttributeIdType::FIELD_ATTR_ID => $attrIdShouldBeProcessed,
            ],
            $attrNameShouldBeProcessedInt => [
                Value::FIELD_VALUE => $intValShouldBeProcessed,
                AttributeIdType::FIELD_ATTR_ID => null,
            ],
        ], $attrVal);
    }

    public function testGetAttrsValMapEmpty(): void
    {
        $SUT = Command::build('');

        $attrVal = $SUT->getAttrsValMap();

        self::assertEquals([], $attrVal);
    }
}
