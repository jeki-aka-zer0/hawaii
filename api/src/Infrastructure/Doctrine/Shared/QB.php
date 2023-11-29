<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Shared;

use App\Domain\Shared\Util\Str;
use Doctrine\DBAL\Query\QueryBuilder as DBALQB;
use Doctrine\ORM\QueryBuilder as ORMQB;
use RuntimeException;

/**
 * @method ORMQB getORMQB()
 * @method DBALQB getDBALQB()
 */
final readonly class QB
{
    public const DESC = 'DESC';
    private const QBs = ['getDBALQB', 'getORMQB'];

    public function __construct(private DBALQB|ORMQB $qb)
    {
    }

    public function whereFieldLike(string $field, ?string $searchStr, string $alias = null): self
    {
        if ($searchStr) {
            $withAlias = join('.', array_filter([$alias, $field]));
            $this->qb
                ->where($this->qb->expr()->like(sprintf('lower(%s)', $withAlias), sprintf(':%s', $field)))
                ->setParameter($field, '%'.(new Str($searchStr))->low().'%');
        }

        return $this;
    }

    public function __call(string $name, array $arguments)
    {
        if (in_array($name, self::QBs, true)) {
            return $this->qb;
        }

        throw new RuntimeException(sprintf('Unexpected %s method call', $name));
    }
}
