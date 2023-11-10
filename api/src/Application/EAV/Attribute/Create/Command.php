<?php

declare(strict_types=1);

namespace App\Application\EAV\Attribute\Create;

use App\Application\Shared\Field;
use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Infrastructure\UI\Web\Request\CommandInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class Command implements CommandInterface
{
    public const FIELD_NAME = 'name';

    #[Assert\NotBlank, Assert\Length(min: 2, max: 255)]
    public string $name;

    #[Assert\NotBlank, Assert\Choice(callback: 'getAttributeTypesList'/** @link Command::getAttributeTypesList */)]
    public string $type;

    public static function getAttributeTypesList(): array
    {
        return array_map(static fn(AttributeType $t) => $t->value, AttributeType::cases());
    }

    public function getNameField(): Field
    {
        return new Field(Attribute::LABEL, self::FIELD_NAME, $this->name);
    }

    public static function build(string $name, AttributeType $type): self
    {
        $cmd = new self();
        $cmd->name = $name;
        $cmd->type = $type->value;

        return $cmd;
    }
}
