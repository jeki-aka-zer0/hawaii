<?php

declare(strict_types=1);

namespace App\Domain\EAV\Attribute\Entity;

use App\Domain\EAV\Value\Entity\Value;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeIdType;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeTypeType;
use App\Infrastructure\Doctrine\EAV\Attribute\DbAttributeRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DbAttributeRepository::class)]
final class Attribute
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    #[ORM\OneToMany('attribute', Value::class, ['all'], orphanRemoval: true)]
    private Collection $values;

    public function __construct(
        #[ORM\Id, ORM\Column(type: AttributeIdType::NAME)]
        private AttributeId $attributeId,

        #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
        private string $name,

        #[ORM\Column(type: AttributeTypeType::NAME, options: ['default' => AttributeTypeType::STRING])]
        private AttributeType $type,

        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private DateTimeImmutable $createdAt = new DateTimeImmutable()
    ) {
        $this->updatedAt = $createdAt;
        $this->values = new ArrayCollection();
    }

    public function isNameMatch(string $name): bool
    {
        return mb_strtolower(trim($name)) === mb_strtolower($this->name);
    }
}
