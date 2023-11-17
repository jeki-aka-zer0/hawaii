<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Attribute;

use App\Application\EAV\Builder;
use App\Domain\EAV\Attribute\Entity\Attribute;
use PHPUnit\Framework\TestCase;

final class AttributeTest extends TestCase
{
    private static Attribute $alreadyExistentAttribute;

    public function namesDataProvider(): array
    {
        return [
            'same names' => [
                'name' => self::getAlreadyExistentAttribute()->name,
                'isNameMatchExpected' => true,
            ],
            'same names in different case and with spaces' => [
                'name' => sprintf(' %s ', mb_strtoupper(self::getAlreadyExistentAttribute()->name)),
                'isNameMatchExpected' => true,
            ],
            'different names' => [
                'name' => 'some another name',
                'isNameMatchExpected' => false,
            ],
        ];
    }

    /**
     * @dataProvider namesDataProvider
     */
    public function testIsNameMatch(string $name, bool $isNameMatchExpected): void
    {
        $isNameMatch = self::getAlreadyExistentAttribute()->isNameMatch($name);

        self::assertEquals($isNameMatchExpected, $isNameMatch);
    }

    private static function getAlreadyExistentAttribute(): Attribute
    {
        return self::$alreadyExistentAttribute ??= Builder::buildAttribute();
    }
}
