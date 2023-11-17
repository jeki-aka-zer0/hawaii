<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Entity;

use App\Application\EAV\Builder;
use App\Domain\EAV\Entity\Entity\Entity;
use PHPUnit\Framework\TestCase;

final class EntityTest extends TestCase
{
    private static Entity $alreadyExistentEntity;

    public function namesDataProvider(): array
    {
        return [
            'same names' => [
                'name' => self::getAlreadyExistentEntity()->name,
                'isNameMatchExpected' => true,
            ],
            'same names in different case and with spaces' => [
                'name' => sprintf(' %s ', mb_strtoupper(self::getAlreadyExistentEntity()->name)),
                'isNameMatchExpected' => true,
            ],
            'different names' => [
                'name' => Builder::getRandomEntityName(self::getAlreadyExistentEntity()->name),
                'isNameMatchExpected' => false,
            ],
        ];
    }

    /**
     * @dataProvider namesDataProvider
     */
    public function testIsNameMatch(string $name, bool $isNameMatchExpected): void
    {
        $isNameMatch = self::getAlreadyExistentEntity()->isNameMatch($name);

        self::assertEquals($isNameMatchExpected, $isNameMatch);
    }

    private static function getAlreadyExistentEntity(): Entity
    {
        return self::$alreadyExistentEntity ??= Builder::buildEntity();
    }
}
