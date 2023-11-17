<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Attribute;

use App\Application\EAV\Builder;
use App\Domain\EAV\Attribute\Entity\Attribute;
use PHPUnit\Framework\TestCase;

final class AttributeTest extends TestCase
{
    private static Attribute $alreadyExistentAttr;

    public function namesDataProvider(): array
    {
        return [
            'same names' => [
                'name' => self::getAlreadyExistentAttr()->name,
                'isNameMatchExpected' => true,
            ],
            'same names in different case and with spaces' => [
                'name' => sprintf(' %s ', mb_strtoupper(self::getAlreadyExistentAttr()->name)),
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
        $isNameMatch = self::getAlreadyExistentAttr()->isNameMatch($name);

        self::assertEquals($isNameMatchExpected, $isNameMatch);
    }

    private static function getAlreadyExistentAttr(): Attribute
    {
        return self::$alreadyExistentAttr ??= Builder::buildAttr();
    }
}
