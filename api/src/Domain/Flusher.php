<?php

declare(strict_types=1);

namespace App\Domain;

interface Flusher
{
    public function flush(): void;
}
