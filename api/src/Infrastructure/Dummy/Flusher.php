<?php

declare(strict_types=1);

namespace App\Infrastructure\Dummy;

final class Flusher implements \App\Domain\Flusher
{
    public function __construct(private bool $flushed = false)
    {
    }

    public function flush(): void
    {
        $this->flushed = true;
    }

    public function isFlushed(): bool
    {
        return $this->flushed;
    }
}
