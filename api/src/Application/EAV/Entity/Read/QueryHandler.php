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
use App\Infrastructure\Doctrine\EAV\Value\ValueIdType;
use App\Infrastructure\Doctrine\Shared\QB;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class QueryHandler
{
    public const KEY_ATTRIBUTES_VALUES = 'attributes_values';

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
        $entity = $this->selectEntityFields($this->qbFromEntity())
            ->where(sprintf('%s = :entity_id', EntityIdType::FIELD_ENTITY_ID))
            ->setParameter('entity_id', $entityId->getValue())
            ->fetchAssociative() ?: throw EntityNotFoundException::byId($entityId, Entity::NAME);

        return $this->addAttributesValues([$entity])[0];
    }

    private function getBasicQueryBuilder(Query $query): QueryBuilder
    {
        return (new QB($this->qbFromEntity()))
            ->whereFieldLike(Entity::FIELD_NAME, $query->name)
            ->getQb();
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
        $entities = $this->selectEntityFields($qb)
            ->setFirstResult($query->offset)
            ->setMaxResults($query->limit)
            ->orderBy(Entity::FIELD_CREATED_AT, QB::DESC)
            ->fetchAllAssociative();

        return $this->addAttributesValues($entities);
    }

    private function qbFromEntity(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()->from(Entity::NAME);
    }

    private function selectEntityFields(QueryBuilder $qb): QueryBuilder
    {
        return $qb->select(EntityIdType::FIELD_ENTITY_ID, Entity::FIELD_NAME, Entity::FIELD_DESCRIPTION);
    }

    private function addAttributesValues(array $entities): array
    {
        $entityIds = array_map(static fn(array $row): string => $row[EntityIdType::FIELD_ENTITY_ID], $entities);

        /**
         * @noinspection PhpUnhandledExceptionInspection
         * @var array<int, array{entity_id: string, name: string, value: string}> $attributesValues
         */
        $attributesValues = $this->connection->createQueryBuilder()
            ->select(
                sprintf('v.%s', ValueIdType::FIELD_VALUE_ID),
                sprintf('a.%s', Attribute::FIELD_NAME),
                sprintf('v.%f', Value::FIELD_VALUE)
            )
            ->from(Value::NAME, 'v')
            ->leftJoin(
                'v',
                Attribute::NAME,
                'a',
                sprintf('a.%s = v.%s', AttributeIdType::FIELD_ATTRIBUTE_ID, AttributeIdType::FIELD_ATTRIBUTE_ID)
            )
            ->where(sprintf('v.%s IN (:entity_ids)', ValueIdType::FIELD_VALUE_ID))
            ->setParameter('entity_ids', $entityIds, ArrayParameterType::STRING)
            ->fetchAllAssociative();

        $attributesValuesMap = [];
        foreach ($attributesValues as $row) {
            $attributesValuesMap[$row[ValueIdType::FIELD_VALUE_ID]][] = [
                Attribute::FIELD_NAME => $row[Attribute::FIELD_NAME],
                Value::FIELD_VALUE => $row[Value::FIELD_VALUE],
            ];
        }

        foreach ($entities as $i => $entity) {
            $entities[$i][self::KEY_ATTRIBUTES_VALUES] = [];
            if (isset($attributesValuesMap[$entity[ValueIdType::FIELD_VALUE_ID]])) {
                $entities[$i][self::KEY_ATTRIBUTES_VALUES] = $attributesValuesMap[$entity[ValueIdType::FIELD_VALUE_ID]];
            }
        }

        return $entities;
    }
}
