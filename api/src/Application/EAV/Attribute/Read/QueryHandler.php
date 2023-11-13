<?php

declare(strict_types=1);

namespace App\Application\EAV\Attribute\Read;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Infrastructure\Doctrine\Shared\QB;
use Doctrine\DBAL\Connection;

final readonly class QueryHandler
{
    public function __construct(private Connection $connection)
    {
    }

    public function fetch(Query $query): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return (new QB(
            $this->connection
                ->createQueryBuilder()
                ->select(Attribute::FIELD_NAME)
                ->from(Attribute::NAME)
        ))
            ->whereFieldLike(Attribute::FIELD_NAME, $query->name)
            ->getQb()
            ->fetchAllAssociative();
    }
}
