<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Shared;

use App\Domain\Shared\Util\Str;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class QB
{
    public const DESC = 'DESC';

    public function __construct(private QueryBuilder $qb)
    {
    }

    public function whereFieldLike(string $field, string $searchStr): self
    {
        if ($searchStr) {
            $this->qb
                ->where($this->qb->expr()->like(sprintf('lower(%s)', $field), sprintf(':%s', $field)))
                ->setParameter($field, '%'.(new Str($searchStr))->low().'%');
        }

        return $this;
    }

    public function getQb(): QueryBuilder
    {
        return $this->qb;
    }
}
