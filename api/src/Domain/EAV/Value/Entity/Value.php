<?php

declare(strict_types=1);

namespace App\Domain\EAV\Value\Entity;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeIdType;
use App\Infrastructure\Doctrine\EAV\Entity\EntityIdType;
use App\Infrastructure\Doctrine\EAV\Value\DbValueRepository;
use App\Infrastructure\Doctrine\EAV\Value\ValueIdType;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: DbValueRepository::class)]
final class Value
{
    public const NAME = 'value';
    public const FIELD_VALUE = 'value';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        #[ORM\Id, ORM\Column(type: ValueIdType::FIELD_VALUE_ID)]
        readonly public ValueId $valueId,

        #[ORM\ManyToOne(targetEntity: Entity::class, inversedBy: 'values')]
        #[ORM\JoinColumn(name: EntityIdType::FIELD_ENTITY_ID, referencedColumnName: EntityIdType::FIELD_ENTITY_ID, nullable: false, onDelete: 'CASCADE')]
        public Entity $entity,

        #[ORM\ManyToOne(targetEntity: Attribute::class, inversedBy: 'values')]
        #[ORM\JoinColumn(name: AttributeIdType::FIELD_ATTR_ID, referencedColumnName: AttributeIdType::FIELD_ATTR_ID, nullable: false, onDelete: 'CASCADE')]
        public Attribute $attribute,

        #[ORM\Column(type: Types::STRING, length: 255)]
        public string|int $value,

        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private DateTimeImmutable $createdAt = new DateTimeImmutable()
    ) {
        $this->updatedAt = $createdAt;

        $this->updateVal($this->value);
    }

    public function updateVal(int|string $val): void
    {
        $this->value = match ($this->attribute->type) {
            AttributeType::String => trim((string)$val),
            AttributeType::Int => (int)$val,
        };
        $this->updatedAt = new DateTimeImmutable();
    }

    public function isEqual(ValueId $valId = null, EntityId $entityId = null, AttributeId $attrId = null): bool
    {
        $isValIdEmpty = $valId === null;
        $isEntityIdEmpty = $entityId === null;
        $isAttrIdEmpty = $attrId === null;

        if ($isValIdEmpty && $isEntityIdEmpty && $isAttrIdEmpty) {
            throw new InvalidArgumentException(sprintf('At least one of the parameters must be set to compare %s entity', self::NAME));
        }

        return ($isValIdEmpty || $this->valueId->isEqual($valId)) &&
            ($isEntityIdEmpty || $this->entity->isEqual($entityId)) &&
            ($isAttrIdEmpty || $this->attribute->isEqual($attrId));
    }
}
