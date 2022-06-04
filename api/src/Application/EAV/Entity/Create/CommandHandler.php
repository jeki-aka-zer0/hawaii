<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Create;

use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Entity\Repository\EntityRepository;
use App\Domain\Flusher;
use DateTimeImmutable;
use DomainException;

final class CommandHandler
{
    public function __construct(
        private readonly EntityRepository $entities,
        private readonly Flusher $flusher
    ) {
    }

    public function handle(Command $command): EntityId
    {
        $trimmedName = trim($command->name);
        if ($this->entities->hasByName($trimmedName)) {
            throw new DomainException(sprintf('An entity with the name "%s" already exists.', $command->name));
        }

        $this->entities->add(
            new Entity(
                $entityId = EntityId::generate(),
                $trimmedName,
                $command->description,
                new DateTimeImmutable()
            )
        );

        $this->flusher->flush();

        return $entityId;
    }
}
