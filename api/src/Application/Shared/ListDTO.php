<?php

declare(strict_types=1);

namespace App\Application\Shared;

final class ListDTO
{
    public function __construct(
        readonly public int $count,
        readonly public array $results,
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
