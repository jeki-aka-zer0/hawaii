<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV\Entity;

use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Entity\Repository\EntityRepository;
use App\Domain\Shared\Repository\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DbEntityRepository extends ServiceEntityRepository implements EntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass = Entity::class)
    {
        parent::__construct($registry, $entityClass);
    }

    public function get(EntityId $entityId): Entity
    {
        $entity = $this->find($entityId->getValue());
        if ($entity === null) {
            throw EntityNotFoundException::byId($entityId, Entity::NAME);
        }

        return $entity;
    }

    public function hasByName(string $name): bool
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->createQueryBuilder('e')
                ->select('COUNT(e.entityId)')
                ->andWhere(sprintf('lower(e.%s) = :name', Entity::FIELD_NAME))
                ->setParameter(':name', $name)
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    public function add(Entity $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }
}
