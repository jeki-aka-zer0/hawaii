<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Read;

use App\Application\Shared\ListDTO;
use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Value\Entity\Value;
use App\Domain\Shared\Repository\EntityNotFoundException;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeIdType;
use App\Infrastructure\Doctrine\EAV\Entity\EntityIdType;
use App\Infrastructure\Doctrine\Shared\QB;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class QueryHandler
{
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
        $entity = $this->select($this->qbFrom())
            ->where(sprintf('%s = :entity_id', EntityIdType::FIELD_ENTITY_ID))
            ->setParameter('entity_id', $entityId->getVal())
            ->fetchAssociative() ?: throw EntityNotFoundException::byId($entityId, Entity::NAME);

        return $this->addAttrsVal([$entity])[0];
    }

    private function getBasicQueryBuilder(Query $query): QueryBuilder
    {
        return (new QB($this->qbFrom()))
            ->whereFieldLike(Entity::FIELD_NAME, $query->name)
            ->getDBALQB();
    }

    private function getCount(QueryBuilder $qb): int
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return (int)$qb->select(['COUNT(*)'])->fetchOne();
    }

    /**
     * @return array<int, array{
     *     entity_id: string,
     *     name: string,
     *     description: ?string,
     *     attributes_values: array<int, array{
     *          name: string,
     *          value: string|int
     *      }>
     *     }>
     */
    private function getResults(QueryBuilder $qb, Query $query): array
    {
        /**
         * @noinspection PhpUnhandledExceptionInspection
         * @var array<int, array{
         *     entity_id: string,
         *     name: string,
         *     description: ?string> $entities
         */
        $entities = $this->select($qb)
            ->setFirstResult($query->offset)
            ->setMaxResults($query->limit)
            ->orderBy(Entity::FIELD_CREATED_AT, QB::DESC)
            ->fetchAllAssociative();

        return $this->addAttrsVal($entities);
    }

    private function qbFrom(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()->from(Entity::NAME);
    }

    private function select(QueryBuilder $qb): QueryBuilder
    {
        return $qb->select(EntityIdType::FIELD_ENTITY_ID, Entity::FIELD_NAME, Entity::FIELD_DESCRIPTION);
    }

    private function addAttrsVal(array $entities): array
    {
        $entityIds = array_map(static fn(array $row): string => $row[EntityIdType::FIELD_ENTITY_ID], $entities);

        /**
         * @noinspection PhpUnhandledExceptionInspection
         * @var array<int, array{entity_id: string, name: string, value: string}> $attrsVal
         */
        $attrsVal = $this->connection->createQueryBuilder()
            ->select(
                sprintf('v.%s', EntityIdType::FIELD_ENTITY_ID),
                sprintf('a.%s', Attribute::FIELD_NAME),
                sprintf('v.%s', Value::FIELD_VALUE)
            )
            ->from(Value::NAME, 'v')
            ->leftJoin(
                'v',
                Attribute::NAME,
                'a',
                sprintf('a.%s = v.%s', AttributeIdType::FIELD_ATTR_ID, AttributeIdType::FIELD_ATTR_ID)
            )
            ->where(sprintf('v.%s IN (:entity_ids)', EntityIdType::FIELD_ENTITY_ID))
            ->setParameter('entity_ids', $entityIds, ArrayParameterType::STRING)
            ->fetchAllAssociative();

        $attrsValMap = [];
        foreach ($attrsVal as $row) {
            $attrsValMap[$row[EntityIdType::FIELD_ENTITY_ID]][] = [
                Attribute::FIELD_NAME => $row[Attribute::FIELD_NAME],
                Value::FIELD_VALUE => $row[Value::FIELD_VALUE],
            ];
        }

        foreach ($entities as $i => $entity) {
            $entities[$i][Attribute::KEY_ATTRS_VALUES] = $attrsValMap[$entity[EntityIdType::FIELD_ENTITY_ID]] ?? [];
        }

        return $entities;
    }
}
