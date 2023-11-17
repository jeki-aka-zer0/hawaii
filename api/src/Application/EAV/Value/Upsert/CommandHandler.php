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

final readonly class CommandHandler
{
    public function __construct(
        private ValueRepository $values,
        private AttributeRepository $attributes,
        private EntityRepository $entities,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $cmd): ValueId
    {
        $entity = $this->entities->get($entityId = new EntityId($cmd->entityId));
        $attribute = $this->attributes->get($attributeId = new AttributeId($cmd->attributeId));
        $value = $this->values->findByEntityAndAttribute($entityId, $attributeId);
        if (null === $value) {
            $this->values->add(
                $value = new Value(
                    ValueId::generate(),
                    $entity,
                    $attribute,
                    $cmd->value,
                )
            );
        } else {
            $value->updateValue($cmd->value);
        }

        $this->flusher->flush();

        return $value->valueId;
    }
}
