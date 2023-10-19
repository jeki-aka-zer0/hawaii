<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Create;

use App\Infrastructure\UI\Web\Request\CommandInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class Command implements CommandInterface
{
    public const FIELD_NAME = 'name';

    #[Assert\NotBlank, Assert\Length(min: 2, max: 255)]
    public string $name;

    #[Assert\Type('string')]
    public ?string $description = null;
}
