<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Read;

use App\Application\Shared\ListDTO;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final class QueryHandler
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function fetch(Query $query): ListDTO
    {
        $qb = $this->getBasicQueryBuilder($query);

        return new ListDTO(
            $this->getCount($qb),
            $this->getResults($qb, $query),
        );
    }

    private function getBasicQueryBuilder(Query $query): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from('entity');

        if (null !== $query->name) {
            $qb
                ->where($qb->expr()->like('lower(name)', ':name'))
                ->setParameter('name', '%'.mb_strtolower($query->name).'%');
        }

        return $qb;
    }

    private function getCount(QueryBuilder $qb): int
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return (int)$qb->select(['COUNT(*)'])->fetchOne();
    }

    private function getResults(QueryBuilder $qb, Query $query): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $qb
            ->select([
                'name',
                'description',
            ])
            ->setFirstResult($query->offset)
            ->setMaxResults($query->limit)
            ->orderBy('lower(name)')
            ->fetchAllAssociative();
    }
}
