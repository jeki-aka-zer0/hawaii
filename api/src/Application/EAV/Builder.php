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
        int|string $value,
        string $entityDescription = null,
        AttributeType $attributeType = AttributeType::String,
    ): void {
        $entityId = $this->buildEntity($entityName, $entityDescription);
        $attributeId = $this->buildAttribute($attributeName, $attributeType);
        $this->buildValue($entityId, $attributeId, $value);
    }

    public function buildEntity(string $name, string $description = null): EntityId
    {
        $cmd = new EntityCommand();
        $cmd->name = $name;
        $cmd->description = $description;

        return $this->entityHandler->handle($cmd);
    }

    public function buildAttribute(string $name, AttributeType $type = AttributeType::String): AttributeId
    {
        $cmd = new AttributeCommand();
        $cmd->name = $name;
        $cmd->type = $type->value;

        return $this->attributeHandler->handle($cmd);
    }

    public function buildValue(EntityId $entityId, AttributeId $attributeId, int|string $value): ValueId
    {
        $cmd = new ValueCommand();
        $cmd->entityId = $entityId->getValue();
        $cmd->attributeId = $attributeId->getValue();
        $cmd->value = $value;

        return $this->valueHandler->handle($cmd);
    }
}
