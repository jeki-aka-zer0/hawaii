<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV\Entity;

use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Entity\Repository\EntityRepository;
use App\Domain\Shared\Repository\EntityNotFoundException;
use App\Infrastructure\Doctrine\Shared\QB;
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
        $entity = $this->find($entityId->getVal());
        if ($entity === null) {
            throw EntityNotFoundException::byId($entityId, Entity::NAME);
        }

        return $entity;
    }

    public function hasByName(string $name): bool
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return (new QB(
                $this
                    ->createQueryBuilder('e')
                    ->select('COUNT(e.entityId)')
            ))
                ->whereFieldLike(Entity::FIELD_NAME, $name, 'e')
                ->getORMQB()
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    public function add(Entity $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }
}
