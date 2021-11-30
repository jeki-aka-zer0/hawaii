<?php

declare(strict_types=1);

namespace App\Domain\EAV\Entity;

use App\Domain\EAV\Repository\EntityRepository;
use App\Infrastructure\Domain\Model\DoctrineEntityIdType;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntityRepository::class)]
final class Entity
{
    #[ORM\Id, ORM\Column(type: DoctrineEntityIdType::NAME)]
    private EntityId $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }
}
