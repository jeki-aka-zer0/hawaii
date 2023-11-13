<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Response\Pagination;

use App\Application\Shared\ListDTO;

final readonly class PaginationDecoratorDTO
{
    private const KEY_PREVIOUS = 'previous';
    private const KEY_NEXT = 'next';

    public function __construct(
        private ListDTO $list,
        private ?string $previous,
        private ?string $next,
    ) {
    }

    public function toArray(): array
    {
        return $this->list->toArray() + [
                self::KEY_PREVIOUS => $this->previous,
                self::KEY_NEXT => $this->next,
            ];
    }
}
