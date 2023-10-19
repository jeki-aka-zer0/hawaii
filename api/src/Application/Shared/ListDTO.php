<?php

declare(strict_types=1);

namespace App\Application\Shared;

final readonly class ListDTO
{
    public function __construct(
        public int $count,
        public array $results,
    ) {
    }

    public function toArray(): array
    {
        return [
            'count' => $this->count,
            'results' => $this->results,
        ];
    }
}
