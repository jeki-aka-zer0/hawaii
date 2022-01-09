<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Attribute;

use PHPUnit\Framework\TestCase;

final class AttributeTest extends TestCase
{
    private const NAMES_DATA_PROVIDER = [
        'same names' => ['name' => AttributeBuilder::TEST_EXISTENT_NAME, 'result' => true],
        'different names' => ['name' => 'some another name', 'result' => false],
    ];

    public function namesDataProvider(): array
    {
        return self::NAMES_DATA_PROVIDER;
    }

    /**
     * @dataProvider namesDataProvider
     * @param string $name
     * @param bool $result
     * @return void
     */
    public function testIsNameMatch(string $name, bool $result): void
    {
        $attribute = (new AttributeBuilder())->build();

        $isNameMatch = $attribute->isNameMatch($name);

        self::assertEquals($result, $isNameMatch);
    }
}
