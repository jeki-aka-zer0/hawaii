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
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    #[ORM\OneToMany('entity', Value::class, ['all'], orphanRemoval: true)]
    private Collection $values;

    public function __construct(
        #[ORM\Id, ORM\Column(type: EntityIdType::NAME)]
        private EntityId $entityId,

        #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
        private string $name,

        #[ORM\Column(type: Types::TEXT, nullable: true)]
        private ?string $description,

        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private DateTimeImmutable $createdAt = new DateTimeImmutable()
    ) {
        $this->updatedAt = $createdAt;
        $this->values = new ArrayCollection();
    }

    public function isNameMatch(string $name): bool
    {
        return trim($name) === $this->name;
    }
}
