<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Attribute;

use App\Domain\EAV\Attribute\Entity\AttributeId;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class AttributeIdTest extends TestCase
{
    public function testConstructShouldFailWhenValIsNotUuid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AttributeId('invalid-uuid');
    }

    public function testGenerateAndToStringAreEquals(): void
    {
        $uuid = AttributeId::generate()->getVal();

        $attrId = new AttributeId($uuid);

        self::assertEquals($uuid, $attrId->getVal());
        self::assertEquals($uuid, (string)$attrId);
    }
}
