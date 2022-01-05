<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV\Entity;

use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Repository\EntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DbEntityRepository extends ServiceEntityRepository implements EntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass = Entity::class)
    {
        parent::__construct($registry, $entityClass);
    }

    public function hasByName(string $name): bool
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.entityId)')
            ->andWhere('e.name = :name')
            ->setParameter(':name', $name)
            ->getQuery()->getSingleScalarResult() > 0;
    }

    public function add(Entity $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }
}
