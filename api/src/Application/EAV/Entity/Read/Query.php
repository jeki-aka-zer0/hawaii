<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Read;

use App\Infrastructure\UI\Web\Request\QueryListInterface;
use App\Infrastructure\UI\Web\Response\Pagination\Paginator;
use Symfony\Component\Validator\Constraints as Assert;

final class Query implements QueryListInterface
{
    public const KEY_SEARCH = 'search';

    #[Assert\Length(min: 2, max: 255)]
    public string|int|null $search = null;

    #[Assert\PositiveOrZero]
    public int $offset = 0;

    #[Assert\GreaterThanOrEqual(1), Assert\LessThanOrEqual(1000)]
    public int $limit = 100;

    public function toArray(): array
    {
        return [
            self::KEY_SEARCH => $this->search,
            Paginator::KEY_OFFSET => $this->offset,
            Paginator::KEY_LIMIT => $this->limit,
        ];
    }

    public static function build(string|int|null $search): Query
    {
        $query = new self();
        $query->search = $search;

        return $query;
    }
}
