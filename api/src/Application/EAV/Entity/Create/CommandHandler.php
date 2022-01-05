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
    public function __construct(private EntityRepository $entities, private Flusher $flusher)
    {
    }

    public function handle(Command $command): EntityId
    {
        $searchName = trim($command->name);
        if ($this->entities->hasByName($searchName)) {
            throw new DomainException(sprintf('An entity with the name "%s" already exists.', $searchName));
        }

        $this->entities->add(
            new Entity(
                $entityId = EntityId::generate(),
                $command->name,
                $command->description,
                new DateTimeImmutable()
            )
        );

        $this->flusher->flush();

        return $entityId;
    }
}
