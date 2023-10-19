<?php

declare(strict_types=1);

namespace App\Application\EAV\Attribute\Create;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\EAV\Attribute\Repository\AttributeRepository;
use App\Domain\Flusher;
use App\Domain\Shared\Repository\FieldException;
use App\Domain\Shared\Util\Err;
use App\Domain\Shared\Util\Str;
use DateTimeImmutable;

final readonly class CommandHandler
{
    public function __construct(
        private AttributeRepository $attributes,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $cmd): AttributeId
    {
        $nameTrimmed = (string)(new Str($cmd->name))->trim();
        if ($this->attributes->hasByName($nameTrimmed)) {
            throw FieldException::build(
                Command::FIELD_NAME,
                Err::alreadyExists(
                    Attribute::LABEL,
                    (string)(new Str(Command::FIELD_NAME))->humanize()->upFirst(),
                    $cmd->name
                ),
            );
        }

        $this->attributes->add(
            new Attribute(
                $attributeId = AttributeId::generate(),
                $nameTrimmed,
                AttributeType::from($cmd->type),
                new DateTimeImmutable()
            )
        );

        $this->flusher->flush();

        return $attributeId;
    }
}
