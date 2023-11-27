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

    public function get(ValueId $valId): Value
    {
        foreach ($this as $val) {
            /** @var Value $val */
            if ($val->isEqual($valId)) {
                return $val;
            }
        }

        throw EntityNotFoundException::byId($valId, Value::NAME);
    }

    public function add(Value $val): void
    {
        $this->attach($val);
    }

    public function findByEntityAndAttr(EntityId $entityId, AttributeId $attrId): ?Value
    {
        foreach ($this as $val) {
            /** @var Value $val */
            if ($val->isEqual(entityId: $entityId, attrId: $attrId)) {
                return $val;
            }
        }

        return null;
    }

    public function hasByValEntityAndAttrNames(string|int $valRaw, string $entityName, string $attrName): ?bool
    {
        foreach ($this as $val) {
            /** @var Value $val */
            if ($val->value === $valRaw && $val->entity->name === $entityName && $val->attribute->name === $attrName) {
                return true;
            }
        }

        return false;
    }
}
