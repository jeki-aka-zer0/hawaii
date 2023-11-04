<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Read;

use App\Application\Shared\ListDTO;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\Shared\Repository\EntityNotFoundException;
use App\Infrastructure\Doctrine\EAV\Entity\EntityIdType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class QueryHandler
{
    private const SELECT = [
        EntityIdType::NAME,
        'name',
        'description',
    ];

    public function __construct(private Connection $connection)
    {
    }

    public function read(Query $query): ListDTO
    {
        $qb = $this->getBasicQueryBuilder($query);

        return new ListDTO(
            $this->getCount($qb),
            $this->getResults($qb, $query),
        );
    }

    /**
     * @return array{entity_id: string, name: string, description: ?string}
     */
    public function oneOrFail(EntityId $entityId): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->qbFromEntity()
            ->select(self::SELECT)
            ->where(sprintf('%s = :entity_id', EntityIdType::NAME))
            ->setParameter('entity_id', $entityId->getValue())
            ->fetchAssociative() ?: throw EntityNotFoundException::byId($entityId, 'Entity not found');
    }

    private function getBasicQueryBuilder(Query $query): QueryBuilder
    {
        $qb = $this->qbFromEntity();

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

    /**
     * @return array{entity_id: string, name: string, description: ?string}
     */
    private function getResults(QueryBuilder $qb, Query $query): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $qb
            ->select(self::SELECT)
            ->setFirstResult($query->offset)
            ->setMaxResults($query->limit)
            ->orderBy('created_at', 'DESC')
            ->fetchAllAssociative();
    }

    private function qbFromEntity(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()->from('entity');
    }
}
