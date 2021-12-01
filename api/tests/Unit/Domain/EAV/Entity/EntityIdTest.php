<?php

declare(strict_types=1);

namespace Unit\Domain\EAV\Entity;

use App\Domain\EAV\Entity\EntityId;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class EntityIdTest extends TestCase
{
    public function testConstructShouldFailWhenValueIsNotUuid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new EntityId('invalid-uuid');
    }

    public function testGenerateAndToStringAreEquals(): void
    {
        $uuid = EntityId::generate()->getValue();

        $entityId = new EntityId($uuid);

        self::assertEquals($uuid, $entityId->getValue());
        self::assertEquals($uuid, (string)$entityId);
    }
}
