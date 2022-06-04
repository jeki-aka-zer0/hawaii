<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Entity;

use PHPUnit\Framework\TestCase;

final class EntityTest extends TestCase
{
    private const NAMES_DATA_PROVIDER = [
        'same names' => [
            'name' => EntityBuilder::TEST_EXISTENT_NAME,
            'isNameMatchExpected' => true,
        ],
        'same names in different case and with spaces' => [
            /** @see EntityBuilder::TEST_EXISTENT_NAME */
            'name' => ' TeSt NaMe ',
            'isNameMatchExpected' => true,
        ],
        'different names' => [
            'name' => 'some another name',
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
        $entity = (new EntityBuilder())->build();

        $isNameMatch = $entity->isNameMatch($name);

        self::assertEquals($isNameMatchExpected, $isNameMatch);
    }
}
