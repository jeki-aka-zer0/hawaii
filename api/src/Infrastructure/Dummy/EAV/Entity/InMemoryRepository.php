<?php

declare(strict_types=1);

namespace App\Infrastructure\Dummy\EAV\Entity;

use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Entity\Repository\EntityRepository;
use App\Domain\Shared\Repository\EntityNotFoundException;
use SplObjectStorage;

final class InMemoryRepository extends SplObjectStorage implements EntityRepository
{
    public function __construct(array $collection)
    {
        array_map(fn(Entity $e) => $this->attach($e), $collection);
    }

    public function get(EntityId $entityId): Entity
    {
        foreach ($this as $entity) {
            /** @var Entity $entity */
            if ($entity->isEqual($entityId)) {
                return $entity;
            }
        }

        throw EntityNotFoundException::byId($entityId, Entity::NAME);
    }

    public function hasByName(string $name): bool
    {
        foreach ($this as $entity) {
            /** @var Entity $entity */
            if ($entity->isNameMatch($name)) {
                return true;
            }
        }

        return false;
    }

    public function add(Entity $entity): void
    {
        $this->attach($entity);
    }
}
