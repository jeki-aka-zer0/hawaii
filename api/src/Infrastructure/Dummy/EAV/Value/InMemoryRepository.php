<?php

declare(strict_types=1);

namespace App\Infrastructure\Dummy\EAV\Value;

use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Value\Entity\Value;
use App\Domain\EAV\Value\Entity\ValueId;
use App\Domain\EAV\Value\Repository\ValueRepository;
use App\Domain\Shared\Repository\EntityNotFoundException;
use SplObjectStorage;

final class InMemoryRepository extends SplObjectStorage implements ValueRepository
{
    public function __construct(array $collection)
    {
        array_map(fn(Value $e) => $this->attach($e), $collection);
    }

    public function get(ValueId $valueId): Value
    {
        foreach ($this as $value) {
            /** @var Value $value */
            if ($value->isEqual($valueId)) {
                return $value;
            }
        }

        throw EntityNotFoundException::byId($valueId, Value::LABEL);
    }

    public function add(Value $value): void
    {
        $this->attach($value);
    }

    public function findByEntityAndAttribute(EntityId $entityId, AttributeId $attributeId): ?Value
    {
        foreach ($this as $value) {
            /** @var Value $value */
            if ($value->isEqual(entityId: $entityId, attributeId: $attributeId)) {
                return $value;
            }
        }

        return null;
    }
}
