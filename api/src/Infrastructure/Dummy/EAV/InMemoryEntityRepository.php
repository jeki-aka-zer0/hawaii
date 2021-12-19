<?php

declare(strict_types=1);

namespace App\Infrastructure\Dummy\EAV;

use App\Domain\EAV\Entity\Entity;
use App\Domain\EAV\Repository\EntityRepository;
use JetBrains\PhpStorm\Pure;

final class InMemoryEntityRepository implements EntityRepository
{
    /**
     * @param Entity[] $collection
     */
    private array $collection;

    public function __construct(array $collection)
    {
        array_map(fn(Entity $e) => $this->add($e), $collection);
    }

    #[Pure]
    public function hasByName(string $name): bool
    {
        foreach ($this->collection as $entity) {
            /** @var Entity $entity */
            if ($entity->isNameMatch($name)) {
                return true;
            }
        }

        return false;
    }

    public function add(Entity $entity): void
    {
        $this->collection[$entity->getEntityId()->getValue()] = $entity;
    }
}
