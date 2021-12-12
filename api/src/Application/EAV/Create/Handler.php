<?php

declare(strict_types=1);

namespace App\Application\EAV\Create;

use App\Domain\EAV\Repository\EntityRepository;

final class Handler
{
    public function __construct(private EntityRepository $entities)
    {
    }

    public function handle(Command $command): string
    {
        $isExists = $this->entities->hasByName($command->name);

        return 'test';
    }
}
