<?php

declare(strict_types=1);

namespace App\Application\EAV\Attribute\Read;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Value\Entity\Value;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeIdType;
use App\Infrastructure\Doctrine\EAV\Value\ValueIdType;
use App\Infrastructure\Doctrine\Shared\QB;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

final readonly class QueryHandler
{
    public const KEY_VAL = 'values';

    public function __construct(private Connection $connection)
    {
    }

    public function fetch(Query $query): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $attrs = (new QB(
            $this->connection
                ->createQueryBuilder()
                ->select(AttributeIdType::FIELD_ATTR_ID, Attribute::FIELD_NAME)
                ->from(Attribute::NAME)
        ))
            ->whereFieldLike(Attribute::FIELD_NAME, $query->name)
            ->getDBALQB()
            ->fetchAllAssociative();

        $attrIds = array_map(static fn(array $row): string => $row[AttributeIdType::FIELD_ATTR_ID], $attrs);

        /** @noinspection PhpUnhandledExceptionInspection */
        $attrsVal = $this->connection->createQueryBuilder()
            ->select(
                ValueIdType::FIELD_VALUE_ID,
                AttributeIdType::FIELD_ATTR_ID,
                Value::FIELD_VALUE,
            )
            ->from(Value::NAME)
            ->where(sprintf('%s IN (:attr_ids)', AttributeIdType::FIELD_ATTR_ID))
            ->setParameter('attr_ids', $attrIds, ArrayParameterType::STRING)
            ->orderBy(Value::FIELD_VALUE)
            ->fetchAllAssociative();

        $attrsValMap = [];
        foreach ($attrsVal as $row) {
            $attrsValMap[$row[AttributeIdType::FIELD_ATTR_ID]][] = [
                ValueIdType::FIELD_VALUE_ID => $row[ValueIdType::FIELD_VALUE_ID],
                Value::FIELD_VALUE => $row[Value::FIELD_VALUE],
            ];
        }

        foreach ($attrs as $i => $attr) {
            $attrs[$i][self::KEY_VAL] = $attrsValMap[$attr[AttributeIdType::FIELD_ATTR_ID]] ?? [];
        }

        return $attrs;
    }
}
