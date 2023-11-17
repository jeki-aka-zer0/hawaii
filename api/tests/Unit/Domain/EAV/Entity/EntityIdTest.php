<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Entity;

use App\Domain\EAV\Entity\Entity\EntityId;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class EntityIdTest extends TestCase
{
    public function testConstructShouldFailWhenValIsNotUuid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new EntityId('invalid-uuid');
    }

    public function testGenerateAndToStringAreEquals(): void
    {
        $uuid = EntityId::generate()->getVal();

        $entityId = new EntityId($uuid);

        self::assertEquals($uuid, $entityId->getVal());
        self::assertEquals($uuid, (string)$entityId);
    }
}
