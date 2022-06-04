<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV\Value;

use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Value\Entity\Value;
use App\Domain\EAV\Value\Repository\ValueRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DbValueRepository extends ServiceEntityRepository implements ValueRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass = Value::class)
    {
        parent::__construct($registry, $entityClass);
    }

    public function findByEntityAndAttribute(EntityId $entityId, AttributeId $attributeId): ?Value
    {
        return $this->findOneBy(['entity' => $entityId->getValue(), 'attribute' => $attributeId->getValue()]);
    }

    public function add(Value $value): void
    {
        $this->getEntityManager()->persist($value);
    }
}
