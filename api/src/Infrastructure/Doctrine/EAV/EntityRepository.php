<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EAV;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;

final class EntityRepository extends BaseEntityRepository implements \App\Domain\EAV\Repository\EntityRepository
{
    public function hasByName(string $name): bool
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.entity_id)')
            ->andWhere('e.name = :name')
            ->setParameter(':name', $name)
            ->getQuery()->getSingleScalarResult() > 0;
    }
}
