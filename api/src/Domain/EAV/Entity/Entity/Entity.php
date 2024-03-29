<?php

declare(strict_types=1);

namespace App\Domain\EAV\Entity\Entity;

use App\Domain\EAV\Value\Entity\Value;
use App\Infrastructure\Doctrine\EAV\Entity\DbEntityRepository;
use App\Infrastructure\Doctrine\EAV\Entity\EntityIdType;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DbEntityRepository::class)]
final class Entity
{
    public const NAME = 'entity';
    public const FIELD_NAME = 'name';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_CREATED_AT = 'created_at';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(Entity::NAME, Value::class, ['all'], orphanRemoval: true)]
    private Collection $values;

    public function __construct(
        #[ORM\Id, ORM\Column(type: EntityIdType::FIELD_ENTITY_ID)]
        private EntityId $entityId,

        #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
        public string $name,

        #[ORM\Column(type: Types::TEXT, nullable: true)]
        public ?string $description,

        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private DateTimeImmutable $createdAt = new DateTimeImmutable()
    ) {
        $this->updatedAt = $createdAt;
        $this->values = new ArrayCollection();
    }

    public function isEqual(EntityId $entityId): bool
    {
        return $this->entityId->isEqual($entityId);
    }

    public function isNameMatch(string $name): bool
    {
        return mb_strtolower(trim($name)) === mb_strtolower($this->name);
    }
}
