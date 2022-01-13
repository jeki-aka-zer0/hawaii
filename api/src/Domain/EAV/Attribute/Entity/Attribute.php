<?php

declare(strict_types=1);

namespace App\Domain\EAV\Attribute\Entity;

use App\Infrastructure\Doctrine\EAV\Attribute\AttributeIdType;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeTypeType;
use App\Infrastructure\Doctrine\EAV\Attribute\DbAttributeRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

//Add type to attribute table
// bin/console doctrine:migration:diff
// bin/console doctrine:migration:migrate
// bin/console doctrine:migration:status
// bin/console doctrine:migration:execute 'DoctrineMigrations\Version20220113204012' --down
#[ORM\Entity(repositoryClass: DbAttributeRepository::class)]
final class Attribute
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

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
    }

    public function isNameMatch(string $name): bool
    {
        return trim($name) === $this->name;
    }
}
