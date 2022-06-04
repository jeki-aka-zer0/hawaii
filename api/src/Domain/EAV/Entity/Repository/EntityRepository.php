<?php

declare(strict_types=1);

namespace App\Domain\EAV\Entity\Repository;

use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\Shared\Repository\EntityNotFoundException;

interface EntityRepository
{
    /**
     * @throws EntityNotFoundException
     */
    public function get(EntityId $entityId): Entity;

    public function hasByName(string $name): bool;

    public function add(Entity $entity): void;
}
