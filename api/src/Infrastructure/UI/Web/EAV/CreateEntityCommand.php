<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\EAV;

use App\Infrastructure\UI\Web\Request\CommandInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateEntityCommand implements CommandInterface
{
    #[Assert\NotBlank, Assert\Length(min: 3, max: 255)]
    public string $name;

    #[Assert\NotBlank, Assert\Length(min: 3, max: 255), SerializedName('camel_case')]
    public string $camelCase;
}
