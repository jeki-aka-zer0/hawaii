<?php

declare(strict_types=1);

namespace App\Application\EAV\Value\Upsert;

use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeIdType;
use App\Infrastructure\Doctrine\EAV\Entity\EntityIdType;
use App\Infrastructure\UI\Web\Request\CommandInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class Command implements CommandInterface
{
    #[Assert\NotBlank, Assert\Uuid, SerializedName(EntityIdType::FIELD_ENTITY_ID)]
    public string $entityId;

    #[Assert\NotBlank, Assert\Uuid, SerializedName(AttributeIdType::FIELD_ATTR_ID)]
    public string $attributeId;

    #[Assert\NotBlank, Assert\Type(['string', 'int'])]
    public string|int $value;

    public static function build(EntityId $entityId, AttributeId $attrId, int|string $val): self
    {
        $cmd = new self();
        $cmd->entityId = $entityId->getVal();
        $cmd->attributeId = $attrId->getVal();
        $cmd->value = $val;

        return $cmd;
    }
}
