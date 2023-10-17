<?php

declare(strict_types=1);

namespace App\Application\EAV\Attribute\Create;

use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Infrastructure\UI\Web\Request\CommandInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class Command implements CommandInterface
{
    public const LABEL_NAME = 'Name';

    #[Assert\NotBlank, Assert\Length(min: 2, max: 255)]
    public string $name;

    #[Assert\NotBlank, Assert\Choice(callback: 'getAttributeTypesList'/** @link Command::getAttributeTypesList */)]
    public string $type;

    public static function getAttributeTypesList(): array
    {
        return array_map(static fn(AttributeType $t) => $t->value, AttributeType::cases());
    }
}
