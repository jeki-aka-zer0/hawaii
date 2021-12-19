<?php

declare(strict_types=1);

namespace App\Infrastructure\Dummy;

use App\Domain\Flusher;

final class DummyFlusher implements Flusher
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
