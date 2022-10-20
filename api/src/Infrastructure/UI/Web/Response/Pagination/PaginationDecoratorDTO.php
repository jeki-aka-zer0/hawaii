<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Response\Pagination;

use App\Application\Shared\ListDTO;

final class PaginationDecoratorDTO
{
    public function __construct(
        readonly private ListDTO $list,
        readonly private ?string $previous,
        readonly private ?string $next,
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
