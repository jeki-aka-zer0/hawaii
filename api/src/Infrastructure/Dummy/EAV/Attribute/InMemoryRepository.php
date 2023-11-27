<?php

declare(strict_types=1);

namespace App\Infrastructure\Dummy\EAV\Attribute;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Repository\AttributeRepository;
use App\Domain\Shared\Repository\EntityNotFoundException;
use SplObjectStorage;

final class InMemoryRepository extends SplObjectStorage implements AttributeRepository
{
    public function __construct(array $collection)
    {
        array_map(fn(Attribute $e) => $this->attach($e), $collection);
    }

    public function get(AttributeId $attrId): Attribute
    {
        foreach ($this as $attr) {
            /** @var Attribute $attr */
            if ($attr->isEqual($attrId)) {
                return $attr;
            }
        }

        throw EntityNotFoundException::byId($attrId, Attribute::NAME);
    }

    public function hasByName(string $name): bool
    {
        foreach ($this as $attr) {
            /** @var Attribute $attr */
            if ($attr->isNameMatch($name)) {
                return true;
            }
        }

        return false;
    }

    public function add(Attribute $attr): void
    {
        $this->attach($attr);
    }

    public function find(AttributeId $attrId): ?Attribute
    {
        foreach ($this as $attr) {
            /** @var Attribute $attr */
            if ($attr->isEqual($attrId)) {
                return $attr;
            }
        }

        return null;
    }
}
