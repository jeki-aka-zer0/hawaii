<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Read;

use Doctrine\DBAL\Connection;
use function App\Application\EAV\Read\mb_strtolower;

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
                'description',
            ])
            ->from('entity');

        if (null !== $query->name) {
            $qb
                ->where($qb->expr()->like('lower(name)', ':name'))
                ->setParameter('name', '%'.mb_strtolower($query->name).'%');
        }

        return $qb->fetchAllAssociative();
    }
}