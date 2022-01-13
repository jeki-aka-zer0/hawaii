<?php

declare(strict_types=1);

namespace App\Application\EAV\Attribute\Create;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\EAV\Attribute\Repository\AttributeRepository;
use App\Domain\Flusher;
use DateTimeImmutable;
use DomainException;

final class CommandHandler
{
    public function __construct(private AttributeRepository $attributes, private Flusher $flusher)
    {
    }

    public function handle(Command $command): AttributeId
    {
        $searchName = trim($command->name);
        if ($this->attributes->hasByName($searchName)) {
            throw new DomainException(sprintf('An attribute with the name "%s" already exists.', $searchName));
        }

        $this->attributes->add(
            new Attribute(
                $attributeId = AttributeId::generate(),
                $command->name,
                AttributeType::from($command->type),
                new DateTimeImmutable()
            )
        );

        $this->flusher->flush();

        return $attributeId;
    }
}
