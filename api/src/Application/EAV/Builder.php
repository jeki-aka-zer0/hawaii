<?php

declare(strict_types=1);

namespace App\Application\EAV;

use App\Application\EAV\Attribute\Create\Command as AttributeCommand;
use App\Application\EAV\Attribute\Create\CommandHandler as AttributeHandler;
use App\Application\EAV\Entity\Create\Command as EntityCommand;
use App\Application\EAV\Entity\Create\CommandHandler as EntityHandler;
use App\Application\EAV\Value\Upsert\Command as ValueCommand;
use App\Application\EAV\Value\Upsert\CommandHandler as ValueHandler;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Value\Entity\ValueId;

final readonly class Builder
{
    public function __construct(
        private EntityHandler $entityHandler,
        private AttributeHandler $attributeHandler,
        private ValueHandler $valueHandler,
    ) {
    }

    public function buildAll(
        string $entityName,
        string $attributeName,
        AttributeType $attributeType,
        int|string $value,
        string $entityDescription = null,
    ): void {
        $entityId = $this->buildEntity($entityName, $entityDescription);
        $attributeId = $this->buildAttribute($attributeName, $attributeType);
        $this->buildValue($entityId, $attributeId, $value);
    }

    public function buildEntity(string $name, string $description = null): EntityId
    {
        return $this->entityHandler->handle(EntityCommand::build($name, $description));
    }

    public function buildAttribute(string $name, AttributeType $type): AttributeId
    {
        return $this->attributeHandler->handle(AttributeCommand::build($name, $type));
    }

    public function buildValue(EntityId $entityId, AttributeId $attributeId, int|string $value): ValueId
    {
        return $this->valueHandler->handle(ValueCommand::build($entityId, $attributeId, $value));
    }
}
