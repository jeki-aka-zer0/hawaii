<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Create;

use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Entity\Repository\EntityRepository;
use App\Domain\Flusher;
use App\Domain\Shared\Repository\FieldException;
use App\Domain\Shared\Util\Err;
use App\Domain\Shared\Util\Str;
use DateTimeImmutable;

final readonly class CommandHandler
{
    public function __construct(
        private EntityRepository $entities,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $cmd): EntityId
    {
        $nameTrimmed = (string)(new Str($cmd->name))->trim();
        if ($this->entities->hasByName($nameTrimmed)) {
            throw FieldException::alreadyExists($cmd->getNameField());
        }

        $this->entities->add(
            new Entity(
                $entityId = EntityId::generate(),
                $nameTrimmed,
                (string)(new Str($cmd->description ?? ''))->trim() ?: null,
                new DateTimeImmutable(),
            )
        );

        $this->flusher->flush();

        return $entityId;
    }
}
