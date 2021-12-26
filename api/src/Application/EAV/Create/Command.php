<?php

declare(strict_types=1);

namespace App\Application\EAV\Create;

use App\Infrastructure\UI\Web\Request\CommandQueryInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class Command implements CommandQueryInterface
{
    #[Assert\NotBlank, Assert\Length(min: 2, max: 255)]
    public string $name;

    #[Assert\Type('string')]
    public ?string $description = null;
}
