<?php

declare(strict_types=1);

namespace App\Domain\EAV\Attribute\Repository;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\Shared\Repository\EntityNotFoundException;

interface AttributeRepository
{
    /**
     * @throws EntityNotFoundException
     */
    public function get(AttributeId $attrId): Attribute;

    /**
     * @return ?Attribute
     */
    public function find(AttributeId $attrId);

    public function hasByName(string $name): bool;

    public function findByName(string $name): ?Attribute;

    public function add(Attribute $attr): void;
}
