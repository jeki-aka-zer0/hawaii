<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV\Attribute;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Repository\AttributeRepository;
use App\Domain\Shared\Repository\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DbAttributeRepository extends ServiceEntityRepository implements AttributeRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass = Attribute::class)
    {
        parent::__construct($registry, $entityClass);
    }

    public function get(AttributeId $attributeId): Attribute
    {
        $attribute = $this->find($attributeId->getValue());
        if ($attribute === null) {
            throw EntityNotFoundException::byId($attributeId, Attribute::LABEL);
        }

        return $attribute;
    }

    public function hasByName(string $name): bool
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->createQueryBuilder('a')
                ->select('COUNT(a.attributeId)')
                ->andWhere('lower(a.name) = :name')
                ->setParameter(':name', mb_strtolower($name))
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function add(Attribute $attribute): void
    {
        $this->getEntityManager()->persist($attribute);
    }
}
