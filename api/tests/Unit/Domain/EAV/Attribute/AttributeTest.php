<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Attribute;

use App\Domain\EAV\Attribute\Entity\Attribute;
use PHPUnit\Framework\TestCase;

final class AttributeTest extends TestCase
{
    private const NAMES_DATA_PROVIDER = [
        'same names' => [
            Attribute::FIELD_NAME => AttributeBuilder::TEST_EXISTENT_NAME,
            'isNameMatchExpected' => true,
        ],
        'same names in different case and with spaces' => [
            /** @see AttributeBuilder::TEST_EXISTENT_NAME */
            Attribute::FIELD_NAME => ' CoLoR ',
            'isNameMatchExpected' => true,
        ],
        'different names' => [
            Attribute::FIELD_NAME => 'some another name',
            'isNameMatchExpected' => false,
        ],
    ];

    public function namesDataProvider(): array
    {
        return self::NAMES_DATA_PROVIDER;
    }

    /**
     * @dataProvider namesDataProvider
     */
    public function testIsNameMatch(string $name, bool $isNameMatchExpected): void
    {
        $attribute = (new AttributeBuilder())->build();

        $isNameMatch = $attribute->isNameMatch($name);

        self::assertEquals($isNameMatchExpected, $isNameMatch);
    }
}
