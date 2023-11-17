<?php

declare(strict_types=1);

namespace App\Application\EAV\Attribute\Create;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\EAV\Attribute\Repository\AttributeRepository;
use App\Domain\Flusher;
use App\Domain\Shared\Repository\FieldException;
use App\Domain\Shared\Util\Str;
use DateTimeImmutable;

final readonly class CommandHandler
{
    public function __construct(
        private AttributeRepository $attrs,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $cmd): AttributeId
    {
        if ($this->attrs->hasByName((string)(new Str($cmd->name))->trim()->low())) {
            throw FieldException::alreadyExists($cmd->getNameField());
        }

        $this->attrs->add(
            new Attribute(
                $attrId = AttributeId::generate(),
                (string)(new Str($cmd->name))->trim(),
                AttributeType::from($cmd->type),
                new DateTimeImmutable(),
            )
        );

        $this->flusher->flush();

        return $attrId;
    }
}
