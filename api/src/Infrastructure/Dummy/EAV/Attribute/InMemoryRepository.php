<?php

declare(strict_types=1);

namespace App\Infrastructure\Dummy\EAV\Attribute;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Repository\AttributeRepository;
use App\Domain\Shared\Repository\EntityNotFoundException;
use JetBrains\PhpStorm\Pure;
use SplObjectStorage;

final class InMemoryRepository extends SplObjectStorage implements AttributeRepository
{
    public function __construct(array $collection)
    {
        array_map(fn(Attribute $e) => $this->attach($e), $collection);
    }

    public function get(AttributeId $attributeId): Attribute
    {
        foreach ($this as $attribute) {
            /** @var Attribute $attribute */
            if ($attribute->isEqual($attributeId)) {
                return $attribute;
            }
        }

        throw EntityNotFoundException::byId($attributeId, Attribute::NAME);
    }

    public function hasByName(string $name): bool
    {
        foreach ($this as $attribute) {
            /** @var Attribute $attribute */
            if ($attribute->isNameMatch($name)) {
                return true;
            }
        }

        return false;
    }

    public function add(Attribute $attribute): void
    {
        $this->attach($attribute);
    }
}
