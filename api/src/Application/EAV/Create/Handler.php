<?php

declare(strict_types=1);

namespace App\Application\EAV\Create;

use App\Domain\EAV\Entity\Entity;
use App\Domain\EAV\Entity\EntityId;
use App\Domain\EAV\Repository\EntityRepository;
use App\Domain\Flusher;
use DateTimeImmutable;
use DomainException;

final class Handler
{
    public function __construct(private EntityRepository $entities, private Flusher $flusher)
    {
    }

    public function handle(Command $command): EntityId
    {
        $searchName = trim($command->name);
        if ($this->entities->hasByName($searchName)) {
            throw new DomainException(sprintf('An entity with the name "%s" already exists.', $command->name));
        }

        $entity = new Entity(
            $entityId = EntityId::generate(),
            $command->name,
            $command->description,
            new DateTimeImmutable()
        );

        $this->entities->add($entity);

        $this->flusher->flush();

        return $entityId;
    }
}
