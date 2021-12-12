<?php

declare(strict_types=1);

namespace App\Domain\EAV\Repository;

interface EntityRepository
{
    public function hasByName(string $name): bool;
}
