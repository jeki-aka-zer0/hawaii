<?php

declare(strict_types=1);

namespace App\Domain\EAV\Repository;

use App\Domain\EAV\Entity\Entity;

interface EntityRepository
{
    public function hasByName(string $name): bool;

    public function add(Entity $entity): void;
}
