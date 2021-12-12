<?php

declare(strict_types=1);

namespace App\Application\EAV\Create;

use App\Domain\EAV\Repository\EntityRepository;
use DomainException;

final class Handler
{
    public function __construct(private EntityRepository $entities)
    {
    }

    public function handle(Command $command): string
    {
        $searchName = strtolower(trim($command->name));
        if ($this->entities->hasByName($searchName)) {
            throw new DomainException(sprintf('An entity with the name "%s" already exists.', $command->name));
        }

        return 'test';
    }
}
