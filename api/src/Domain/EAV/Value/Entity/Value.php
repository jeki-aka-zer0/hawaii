<?php

declare(strict_types=1);

namespace App\Domain\EAV\Value\Entity;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeIdType;
use App\Infrastructure\Doctrine\EAV\Entity\EntityIdType;
use App\Infrastructure\Doctrine\EAV\Value\DbValueRepository;
use App\Infrastructure\Doctrine\EAV\Value\ValueIdType;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DbValueRepository::class)]
final class Value
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        #[ORM\Id, ORM\Column(type: ValueIdType::NAME)]
        readonly public ValueId $valueId,

        #[ORM\ManyToOne(targetEntity: Entity::class, inversedBy: 'values')]
        #[ORM\JoinColumn(name: EntityIdType::NAME, referencedColumnName: EntityIdType::NAME, nullable: false, onDelete: 'CASCADE')]
        private Entity $entity,

        #[ORM\ManyToOne(targetEntity: Attribute::class, inversedBy: 'values')]
        #[ORM\JoinColumn(name: AttributeIdType::NAME, referencedColumnName: AttributeIdType::NAME, nullable: false, onDelete: 'CASCADE')]
        private Attribute $attribute,

        #[ORM\Column(type: Types::STRING, length: 255)]
        private string|int $value,

        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private DateTimeImmutable $createdAt = new DateTimeImmutable()
    ) {
        $this->updatedAt = $createdAt;

        $this->updateValue($this->value);
    }

    public function updateValue(int|string $value): void
    {
        $this->value = match ($this->attribute->type) {
            AttributeType::String => trim((string)$value),
            AttributeType::Int => (int)$value,
        };
    }
}
