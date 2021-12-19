<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Entity;

use PHPUnit\Framework\TestCase;

final class EntityTest extends TestCase
{
    private const NAMES_DATA_PROVIDER = [
        'same names' => ['name' => EntityBuilder::TEST_EXISTENT_NAME, 'result' => true],
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
        $entity = (new EntityBuilder())->build();

        $isNameMatch = $entity->isNameMatch($name);

        self::assertEquals($result, $isNameMatch);
    }
}
