<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Entity;

use App\Domain\EAV\Entity\Entity\Entity;
use PHPUnit\Framework\TestCase;

final class EntityTest extends TestCase
{
    private const NAMES_DATA_PROVIDER = [
        'same names' => [
            Entity::FIELD_NAME => EntityBuilder::TEST_EXISTENT_NAME,
            'isNameMatchExpected' => true,
        ],
        'same names in different case and with spaces' => [
            /** @see EntityBuilder::TEST_EXISTENT_NAME */
            Entity::FIELD_NAME => ' TeSt NaMe ',
            'isNameMatchExpected' => true,
        ],
        'different names' => [
            Entity::FIELD_NAME => 'some another name',
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
