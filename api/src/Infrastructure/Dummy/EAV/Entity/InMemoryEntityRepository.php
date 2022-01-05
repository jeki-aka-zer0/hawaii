<?php

declare(strict_types=1);

namespace App\Infrastructure\Dummy\EAV\Entity;

use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Repository\EntityRepository;
use JetBrains\PhpStorm\Pure;
use SplObjectStorage;

final class InMemoryEntityRepository extends SplObjectStorage implements EntityRepository
{
    public function __construct(array $collection)
    {
        array_map(fn(Entity $e) => $this->attach($e), $collection);
    }

    #[Pure]
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
