<?php

declare(strict_types=1);

namespace App\Application\Shared;

final readonly class ListDTO
{
    public const KEY_COUNT = 'count';
    public const KEY_RESULTS = 'results';

    public function __construct(
        public int $count,
        public array $results,
    ) {
    }

    public function toArray(): array
    {
        return [
            self::KEY_COUNT => $this->count,
            self::KEY_RESULTS => $this->results,
        ];
    }
}
