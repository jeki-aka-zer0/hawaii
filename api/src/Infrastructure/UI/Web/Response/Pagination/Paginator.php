<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Response\Pagination;

use App\Application\EAV\Entity\Read\Query;
use App\Application\Shared\ListDTO;

final class Paginator
{
    public const string KEY_OFFSET = 'offset';
    public const string KEY_LIMIT = 'limit';

    public function __construct(
        readonly private Query $query,
        readonly private ListDTO $list,
    ) {
    }

    public function build(): PaginationDecoratorDTO
    {
        return new PaginationDecoratorDTO(
            $this->list,
            $this->getPrevious(),
            $this->getNext()
        );
    }

    private function getPrevious(): ?int
    {
        return $this->isCurrentPageFirst()
            ? null
            : max(0, $this->query->offset - $this->query->limit);
    }

    private function isCurrentPageFirst(): bool
    {
        return $this->query->offset === 0;
    }

    private function getNext(): ?int
    {
        return $this->isCurrentPageLast()
            ? null
            : $this->query->offset + $this->query->limit;
    }

    private function isCurrentPageLast(): bool
    {
        return $this->query->offset + $this->query->limit >= $this->list->count;
    }
}
