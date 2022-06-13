<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Entity;

use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;

final class EntityBuilder
{
    public const TEST_EXISTENT_NAME = 'Test name';

    public function build(EntityId $entityId = null): Entity
    {
        return new Entity($entityId ?? EntityId::generate(), self::TEST_EXISTENT_NAME, 'Test description');
    }
}
