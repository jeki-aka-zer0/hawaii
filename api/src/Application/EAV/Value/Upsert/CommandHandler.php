<?php

declare(strict_types=1);

namespace App\Application\EAV\Value\Upsert;

use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Repository\AttributeRepository;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Entity\Repository\EntityRepository;
use App\Domain\EAV\Value\Entity\Value;
use App\Domain\EAV\Value\Entity\ValueId;
use App\Domain\EAV\Value\Repository\ValueRepository;
use App\Domain\Flusher;

final class CommandHandler
{
    public function __construct(
        private readonly ValueRepository $values,
        private readonly AttributeRepository $attributes,
        private readonly EntityRepository $entities,
        private readonly Flusher $flusher
    ) {
    }

    public function handle(Command $command): ValueId
    {
        $entity = $this->entities->get($entityId = new EntityId($command->entityId));
        $attribute = $this->attributes->get($attributeId = new AttributeId($command->attributeId));
        $value = $this->values->findByEntityAndAttribute($entityId, $attributeId);
        if ($value === null) {
            $this->values->add(
                $value = new Value(
                    ValueId::generate(),
                    $entity,
                    $attribute,
                    $command->value,
                )
            );
        } else {
            $value->updateValue($command->value);
        }

        $this->flusher->flush();

        return $value->valueId;
    }
}
