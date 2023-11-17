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
        private ValueRepository $val,
        private AttributeRepository $attrs,
        private EntityRepository $entities,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $cmd): ValueId
    {
        $entity = $this->entities->get($entityId = new EntityId($cmd->entityId));
        $attr = $this->attrs->get($attrId = new AttributeId($cmd->attributeId));
        $val = $this->val->findByEntityAndAttr($entityId, $attrId);
        if (null === $val) {
            $this->val->add(
                $val = new Value(
                    ValueId::generate(),
                    $entity,
                    $attr,
                    $cmd->value,
                )
            );
        } else {
            $val->updateVal($cmd->value);
        }

        $this->flusher->flush();

        return $val->valueId;
    }
}
