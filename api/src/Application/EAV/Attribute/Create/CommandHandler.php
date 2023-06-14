<?php

declare(strict_types=1);

namespace App\Application\EAV\Attribute\Create;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\EAV\Attribute\Repository\AttributeRepository;
use App\Domain\Flusher;
use App\Domain\Shared\Repository\FieldException;
use DateTimeImmutable;
use DomainException;

final class CommandHandler
{
    public function __construct(
        private readonly AttributeRepository $attributes,
        private readonly Flusher $flusher
    ) {
    }

    public function handle(Command $command): AttributeId
    {
        $trimmedName = trim($command->name);
        if ($this->attributes->hasByName($trimmedName)) {
            throw FieldException::build(
                'name' /** @see \App\Application\EAV\Attribute\Create\Command::$name */,
                sprintf('An attribute with the name "%s" already exists.', $command->name),
            );
        }

        $this->attributes->add(
            new Attribute(
                $attributeId = AttributeId::generate(),
                $trimmedName,
                AttributeType::from($command->type),
                new DateTimeImmutable()
            )
        );

        $this->flusher->flush();

        return $attributeId;
    }
}
