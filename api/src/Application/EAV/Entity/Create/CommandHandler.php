<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Create;

use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Entity\Repository\EntityRepository;
use App\Domain\Flusher;
use App\Domain\Shared\Repository\FieldException;
use DateTimeImmutable;

final class CommandHandler
{
    public function __construct(
        private readonly EntityRepository $entities,
        private readonly Flusher $flusher
    ) {
    }

    public function handle(Command $command): EntityId
    {
        $name = trim($command->name);
        if ($this->entities->hasByName(mb_strtolower($name))) {
            throw FieldException::build(
                'name' /** @see \App\Application\EAV\Entity\Create\Command::$name */,
                sprintf('An entity with the name "%s" already exists.', $name),
            );
        }

        $this->entities->add(
            new Entity(
                $entityId = EntityId::generate(),
                $name,
                trim($command->description) ?: null,
                new DateTimeImmutable()
            )
        );

        $this->flusher->flush();

        return $entityId;
    }
}
