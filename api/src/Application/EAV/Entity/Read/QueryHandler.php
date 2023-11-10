<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Read;

use App\Application\Shared\ListDTO;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\Shared\Repository\EntityNotFoundException;
use App\Infrastructure\Doctrine\EAV\Entity\EntityIdType;
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
        $entity = $this->selectEntityFields($this->qbFromEntity())
            ->where(sprintf('%s = :entity_id', EntityIdType::NAME))
            ->setParameter('entity_id', $entityId->getValue())
            ->fetchAssociative() ?: throw EntityNotFoundException::byId($entityId, Entity::LABEL);

        return $this->addAttributesValues([$entity])[0];
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
            ->orderBy('created_at', 'DESC')
            ->fetchAllAssociative();

        return $this->addAttributesValues($entities);
    }

    private function qbFromEntity(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()->from('entity');
    }

    private function selectEntityFields(QueryBuilder $qb): QueryBuilder
    {
        return $qb->select(EntityIdType::NAME, 'name', 'description');
    }

    private function addAttributesValues(array $entities): array
    {
        $entityIds = array_map(static fn(array $row): string => $row[EntityIdType::NAME], $entities);

        /**
         * @noinspection PhpUnhandledExceptionInspection
         * @var array<int, array{entity_id: string, name: string, value: string}> $attributesValues
         */
        $attributesValues = $this->connection->createQueryBuilder()
            ->select('v.entity_id', 'a.name', 'v.value')
            ->from('value', 'v')
            ->leftJoin('v', 'attribute', 'a', 'a.attribute_id = v.attribute_id')
            ->where('v.entity_id IN (:entity_ids)')
            ->setParameter('entity_ids', $entityIds, ArrayParameterType::STRING)
            ->fetchAllAssociative();

        $attributesValuesMap = [];
        foreach ($attributesValues as $row) {
            $attributesValuesMap[$row['entity_id']][] = ['name' => $row['name'], 'value' => $row['value']];
        }

        foreach ($entities as $i => $entity) {
            $entities[$i]['attributes_values'] = [];
            if (isset($attributesValuesMap[$entity['entity_id']])) {
                $entities[$i]['attributes_values'] = $attributesValuesMap[$entity['entity_id']];
            }
        }

        return $entities;
    }
}
