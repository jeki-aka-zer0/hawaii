<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Response\Pagination;

use App\Application\Shared\ListDTO;

final readonly class PaginationDecoratorDTO
{
    public function __construct(
        private ListDTO $list,
        private ?string $previous,
        private ?string $next,
    ) {
    }

    public function toArray(): array
    {
        return $this->list->toArray() + [
                'previous' => $this->previous,
                'next' => $this->next,
            ];
    }
}
