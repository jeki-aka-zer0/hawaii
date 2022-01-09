<?php

declare(strict_types=1);

namespace App\Application\EAV\Attribute\Read;

use Doctrine\DBAL\Connection;

final class QueryHandler
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function fetch(Query $query): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select([
                'name',
            ])
            ->from('attribute');

        if (null !== $query->name) {
            $qb
                ->where($qb->expr()->like('lower(name)', ':name'))
                ->setParameter('name', '%'.mb_strtolower($query->name).'%');
        }

        return $qb->fetchAllAssociative();
    }
}
